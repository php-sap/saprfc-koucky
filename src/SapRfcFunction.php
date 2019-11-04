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
use phpsap\exceptions\SapException;
use phpsap\exceptions\UnknownFunctionException;
use phpsap\interfaces\Api\IArray;
use phpsap\interfaces\Api\IElement;
use phpsap\interfaces\Api\IValue;
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
     * Invoke the prepared function call.
     * @return array
     * @throws FunctionCallException
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
     * @return void
     * @throws FunctionCallException
     */
    private function setSaprfcParameters()
    {
        $this->setSapRfcInputValues($this->getApi()->getInputValues());
        $this->setSapRfcTables($this->getApi()->getTables());
    }

    /**
     * Set all input values.
     * @param array $inputs The array of input parameters to set.
     * @return void
     * @throws FunctionCallException
     */
    private function setSapRfcInputValues($inputs)
    {
        foreach ($inputs as $input) {
            $name = $input->getName();
            $value = $this->getParam($name);
            if (!$this->setSapRfcInputValue($name, $value, $input->isOptional())) {
                throw new FunctionCallException(sprintf(
                    'Function call %s failed: Assigning param %s, expected type %s, actual type %s failed.',
                    $this->getName(),
                    $name,
                    $input->getType(),
                    gettype($value)
                ));
            }
        }
    }

    /**
     * Set a single input parameter value for the function call.
     * @param string $name The name of the parameter.
     * @param mixed $value The value of the parameter.
     * @param bool $isOptional Is the parameter optional (TRUE) or mandatory (FALSE)?
     * @return bool Has the parameter been set successfully?
     * @throws FunctionCallException in case the parameter value is null but mandatory.
     */
    private function setSapRfcInputValue($name, $value, $isOptional)
    {
        if ($value === null && !$isOptional) {
            throw new FunctionCallException(sprintf(
                'Missing parameter \'%s\' for function call \'%s\'!',
                $name,
                $this->getName()
            ));
        }
        if ($value === null) {
            return true;
        }
        return @saprfc_import($this->function, $name, $value);
    }

    /**
     * Initializes the table parameters of the remote function call.
     * @param array $tables The array of table parameters to initialize.
     * @return void
     * @throws FunctionCallException In case the initialization fails.
     */
    private function setSapRfcTables($tables)
    {
        foreach ($tables as $table) {
            $name = $table->getName();
            $result = $this->setSapRfcTable($name, $this->getParam($name));
            if ($result !== true) {
                throw new FunctionCallException(sprintf(
                    'Initializing table %s for function %s failed!',
                    $name,
                    $this->getName()
                ));
            }
        }
    }

    /**
     * Initialize a remote function call table and add rows, in case there are rows.
     * @param string $name The table name to initialize and fill.
     * @param array $rows The rows to add to the table.
     * @return bool Init success?
     * @throws FunctionCallException
     */
    private function setSapRfcTable($name, $rows)
    {
        if (!@saprfc_table_init($this->function, $name)) {
            return false;
        }
        if (!is_array($rows)) {
            return true;
        }
        foreach ($rows as $number => $row) {
            if (!@saprfc_table_append($this->function, $name, $row)) {
                throw new FunctionCallException(sprintf(
                    'Adding row #%u to table %s for function %s failed!',
                    $number,
                    $name,
                    $this->getName()
                ));
            }
        }
        return true;
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
            $result[$name] = $output->cast($value);
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
     * @return RemoteApi The remote API description class.
     * @throws InvalidArgumentException
     * @throws LogicException In case the given SAP RFC type is missing in the static mapping.
     * @throws SapException In case of a general error where the remote function API cannot be queried.
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
     * Get remote function call API definition.
     * @return array The array describing the remote function call API.
     * @throws SapException In case of a general error where the remote function API cannot be queried.
     */
    public function saprfcFunctionInterface()
    {
        $definitions = @saprfc_function_interface($this->function);
        if ($definitions === false) {
            throw new SapException('Cannot query remote function API!');
        }
        return $definitions;
    }

    /**
     * Create either Value, Struct or Table from a given remote function parameter or return value.
     * @param string $name The name of the parameter or return value.
     * @param string $direction The direction indicating whether it's a parameter or return value.
     * @param bool $optional The flag, whether this parameter or return value is required.
     * @param array $def The parameter or return value definition containing the data type.
     * @return Value|Struct|Table
     * @throws LogicException In case a datatype is missing in the mappings array.
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
     * Create either struct or table members from the def array of the remote function API.
     * @param array $members
     * @return Element[] An array of IElement compatible objects.
     * @throws LogicException In case a datatype is missing in the mappings array.
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
     * @param string $type The ABAP data type from the API definition.
     * @return string The PHP/SAP internal data type.
     * @throws LogicException In case a datatype is missing in the mappings array.
     */
    private function abapToType($type)
    {
        static $mapping = [
            'b'           => IElement::TYPE_INTEGER,  //1-byte integer (internal)
            's'           => IElement::TYPE_INTEGER,  //2-byte integer (internal)
            'I'           => IElement::TYPE_INTEGER,  //4-byte integer
            'INT8'        => IElement::TYPE_INTEGER,  //8-byte integer
            'P'           => IElement::TYPE_FLOAT,    //packed number 1-16 bytes
            'DECFLOAT16'  => IElement::TYPE_FLOAT,    //floating point with 16 positions
            'DECFLOAT34'  => IElement::TYPE_FLOAT,    //floating point with 34 positions
            'F'           => IElement::TYPE_FLOAT,    //binary floating point with 17 positions
            'C'           => IElement::TYPE_STRING,   //fixed length text field
            'N'           => IElement::TYPE_INTEGER,  //fixed length numeric text field 1-262143 positions
            'STRING'      => IElement::TYPE_STRING,   //text string
            'X'           => IElement::TYPE_HEXBIN,   //fixed length hexadecimal encoded binary data
            'XSTRING'     => IElement::TYPE_HEXBIN,   //fixed length hexadecimal encoded binary data
            'D'           => IElement::TYPE_DATE,     //date field
            'T'           => IElement::TYPE_TIME,     //time field
        ];
        if (!array_key_exists($type, $mapping)) {
            throw new LogicException(sprintf('Unknown SAP data type \'%s\'!', $type));
        }
        return $mapping[$type];
    }

    /**
     * Convert SAPRFC type to PHP/SAP direction.
     * @param string $type The remote function parameter type indicating whether it's an input or a return parameter.
     * @return string The PHP/SAP internal direction.
     * @throws LogicException In case the given SAP RFC type is missing in the static mapping.
     */
    private function typeToDirection($type)
    {
        static $mapping = [
            'IMPORT' => IValue::DIRECTION_INPUT,   //SAP remote function input parameter
            'EXPORT' => IValue::DIRECTION_OUTPUT,  //SAP remote function return value or struct
            'TABLE'  => IArray::DIRECTION_TABLE    //SAP remote function return table
        ];
        if (!array_key_exists($type, $mapping)) {
            throw new LogicException(sprintf('Unknown SAPRFC type \'%s\'!', $type));
        }
        return $mapping[$type];
    }
}
