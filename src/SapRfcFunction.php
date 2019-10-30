<?php
/**
 * File src/SapRfcFunction.php
 *
 * PHP/SAP remote function calls using Eduard Kouckys saprfc module.
 *
 * @package saprfc-koucky
 * @author  Gregor J.
 * @license MIT
 */

namespace phpsap\saprfc;

use InvalidArgumentException;
use LogicException;
use phpsap\classes\AbstractFunction;
use phpsap\classes\Api\Element;
use phpsap\classes\Api\Struct;
use phpsap\classes\Api\Table;
use phpsap\classes\Api\Value;
use phpsap\classes\RemoteApi;
use phpsap\exceptions\FunctionCallException;
use phpsap\exceptions\UnknownFunctionException;
use phpsap\interfaces\Api\IArray;
use phpsap\interfaces\Api\IElement;
use phpsap\interfaces\Api\IValue;
use phpsap\interfaces\IApi;
use phpsap\interfaces\IFunction;

/**
 * Class phpsap\saprfc\SapRfcFunction
 *
 * PHP/SAP remote function class abstracting remote function call related functions
 * using Eduard Kouckys saprfc module.
 *
 * @package phpsap\saprfc
 * @author  Gregor J.
 * @license MIT
 */
class SapRfcFunction extends AbstractFunction
{
    /**
     * @var mixed SAP connection resource.
     */
    protected $connection;

    /**
     * @var mixed SAP remote function resource.
     */
    protected $function;

    /**
     * SAP remote function interface.
     * @var mixed
     */
    private $functionInterface;

    /**
     * Invoke the prepared function call.
     * @return array
     * @throws FunctionCallException
     * @throws LogicException
     */
    public function invoke()
    {
        $this->setSaprfcParameters();
        $result = @saprfc_call_and_receive($this->function);
        if ($result !== 0) {
            throw new FunctionCallException(sprintf(
                'Function call %s failed: %s',
                $this->getName(),
                @saprfc_exception($this->function)
            ));
        }
        return $this->getSaprfcResults();
    }

    /**
     * Close remote function call and clear its interface.
     */
    public function __destruct()
    {
        if ($this->functionInterface !== null) {
            $this->functionInterface = null;
        }
        if ($this->function !== null) {
            @saprfc_function_free($this->function);
            $this->function = null;
        }
    }

    /**
     * Set function call parameter.
     * All parameter names will be converted to upper case.
     * @param string                           $name
     * @param array|string|float|int|bool|null $value
     * @return IFunction $this
     * @throws InvalidArgumentException
     */
    public function setParam($name, $value)
    {
        return parent::setParam(
            strtoupper($name),
            $value
        );
    }

    /**
     * Create a remote function call resource.
     * @return mixed
     * @throws UnknownFunctionException
     */
    protected function getFunction()
    {
        $function = @saprfc_function_discover($this->connection, $this->getName());
        if ($function === false) {
            $function = null;
            throw new UnknownFunctionException(sprintf(
                'Unknown function %s: %s',
                $this->getName(),
                @saprfc_error()
            ));
        }
        return $function;
    }

    /**
     * Export all function call parameters.
     * @throws LogicException
     * @throws FunctionCallException
     */
    private function setSaprfcParameters()
    {
        foreach ($this->getApi()->getInputValues() as $input) {
            $name = $input->getName();
            $value = $this->getParam($name);
            if ($value === null && !$input->isOptional()) {
                throw new FunctionCallException(sprintf(
                    'Missing parameter \'%s\' for function call \'%s\'!',
                    $name,
                    $this->getName()
                ));
            }
            $result = @saprfc_import($this->function, $name, $value);
            if ($result !== true) {
                throw new LogicException(sprintf(
                    'Assigning param %s, expected type %s, actual type %s to function %s failed.',
                    $name,
                    $input->getType(),
                    gettype($value),
                    $this->getName()
                ));
            }
        }

        foreach ($this->getApi()->getTables() as $table) {
            $result = @saprfc_table_init($this->function, $table->getName());
            if ($result !== true) {
                throw new LogicException(sprintf(
                    'Initializing table %s for function %s failed.',
                    $table->getName(),
                    $this->getName()
                ));
            }
        }
    }

    /**
     * Import results from the function call.
     * @return array
     */
    private function getSaprfcResults()
    {
        $result = [];
        foreach ($this->getApi()->getOutputValues() as $output) {
            $name = $output->getName();
            $value = @saprfc_export($this->function, $name);
            $result[$name] = $output->cast(trim($value));
            unset($name, $value);
        }
        foreach ($this->getApi()->getTables() as $table) {
            $name = $table->getName();
            $rows = [];
            $max = @saprfc_table_rows($this->function, $name);
            for ($index = 1; $index <= $max; $index++) {
                $rows[] = @saprfc_table_read($this->function, $name, $index);
            }
            $result[$name] = $table->cast($rows);
            unset($name, $rows, $max, $index);
        }
        return $result;
    }

    /**
     * Extract the remote function API and return an API description class.
     * @return IApi
     * @throws InvalidArgumentException
     * @throws LogicException
     */
    public function extractApi()
    {
        $api = new RemoteApi();
        foreach ($this->saprfcFunctionInterface() as $element) {
            $api->add($this->createApiValue(
                strtoupper($element['name']),
                $this->typeToDirection($element['type']),
                (bool)$element['optional'],
                $element['def']
            ));
        }
        return $api;
    }

    /**
     * Get remote function call interface definition.
     * @return array
     */
    private function saprfcFunctionInterface()
    {
        $definitions = @saprfc_function_interface($this->function);
        if ($definitions === false) {
            return [];
        }
        return $definitions;
    }

    /**
     * @param string $name
     * @param string $direction
     * @param bool $optional
     * @param array $def
     * @return IValue
     * @throws LogicException
     */
    private function createApiValue($name, $direction, $optional, $def)
    {
        if ($direction === IArray::DIRECTION_TABLE) {
            return new Table($name, $optional, $this->createMembers($def));
        }
        if ($def[0]['name'] !== '') {
            return new Struct($name, $direction, $optional, $this->createMembers($def));
        }
        return new Value($this->abapToType($def[0]['abap']), $name, $direction, $optional);
    }

    /**
     * Create members from the def array.
     * @param array $members
     * @return array
     * @throws LogicException
     */
    private function createMembers($members)
    {
        $result = [];
        foreach ($members as $member) {
            $result[] = new Element($this->abapToType($member['abap']), $member['name']);
        }
        return $result;
    }

    /**
     * Convert SAPRFC abap datatype to PHP-SAP datatype.
     * @param string $dataType
     * @return string
     * @throws LogicException
     */
    private function abapToType($dataType)
    {
        switch ($dataType) {
            case 'B': //1-byte integer (internal)
                //fall through
            case 'S': //2-byte integer (internal)
                //fall through
            case 'I': //4-byte integer
                //fall through
            case 'INT8': //8-byte integer
                //fall through
            case 'N': //fixed length numeric text field 1-262143 positions
                $result = IElement::TYPE_INTEGER;
                break;
            case 'P': //packed number 1-16 bytes
                //fall through
            case 'DECFLOAT16': //floating point with 16 positions
                //fall through
            case 'DECFLOAT34': //floating point with 34 positions
                //fall through
            case 'F': //binary floating point with 17 positions
                $result = IElement::TYPE_FLOAT;
                break;
            case 'C': //fixed length text field
                //fall through
            case 'STRING': //text string
                $result = IElement::TYPE_STRING;
                break;
            case 'X': //hexadecimal encoded binary data
                //fall through
            case 'XSTRING':
                $result = IElement::TYPE_STRING;
                break;
            case 'D': //date field
                $result = IElement::TYPE_DATE;
                break;
            case 'T': //time field
                $result = IElement::TYPE_TIME;
                break;
            default:
                throw new LogicException(sprintf('Unknown SAP data type \'%s\'!', $dataType));
        }
        return $result;
    }

    /**
     * Convert SAPRFC type to PHP-SAP direction.
     * @param string $type
     * @return string
     * @throws LogicException
     */
    private function typeToDirection($type)
    {
        switch ($type) {
            case 'IMPORT':
                $result = IValue::DIRECTION_INPUT;
                break;
            case 'EXPORT':
                $result = IValue::DIRECTION_OUTPUT;
                break;
            case 'TABLE':
                $result = IArray::DIRECTION_TABLE;
                break;
            default:
                throw new LogicException(sprintf('Unknown SAPRFC type \'%s\'!', $type));
        }
        return $result;
    }
}
