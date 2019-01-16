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

use phpsap\classes\AbstractFunction;
use phpsap\exceptions\FunctionCallException;
use phpsap\exceptions\UnknownFunctionException;

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
     * @throws \phpsap\exceptions\FunctionCallException
     * @throws \LogicException
     */
    protected function execute()
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
     * @return \phpsap\interfaces\IFunction $this
     * @throws \InvalidArgumentException
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
     * @throws \phpsap\exceptions\UnknownFunctionException
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
     * Retrieve the interface of the remote function.
     * @return array
     */
    protected function getRemoteInterface()
    {
        if ($this->functionInterface === null) {
            $this->functionInterface = [];
            foreach ($this->saprfcFunctionInterface() as $element) {
                $this->importRemoteInterfaceElement($element);
            }
        }
        return $this->functionInterface;
    }

    /**
     * Import a remote function call interface element.
     * @param array $element
     */
    private function importRemoteInterfaceElement($element)
    {
        $name = strtoupper($element['name']);
        $type = $this->remoteInterfaceType($element['type'], $element['def']);
        $members = $element['def'];
        $this->functionInterface[$name] = ['type' => $type, 'members' => $members];
    }

    /**
     * Get the remote interface definition type.
     * @param string $type
     * @param array  $def
     * @return string
     */
    private function remoteInterfaceType($type, $def)
    {
        if ($type !== 'TABLE'
            && isset($def[0]['name'])
            && $def[0]['name'] !== ''
        ) {
            return sprintf('%s_STRUCT', $type);
        }
        return $type;
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
     * Export all function call parameters.
     * @throws \LogicException
     */
    private function setSaprfcParameters()
    {
        foreach ($this->getRemoteInterface() as $name => $definition) {
            $result = $this->setSapRfcParameter($name, $definition['type'], $definition['members']);
            if ($result !== true) {
                throw new \LogicException(sprintf(
                    'Assigning param %s, type %s, value %s to function %s failed.',
                    $name,
                    $definition['type'],
                    gettype($this->getParam($name)),
                    $this->getName()
                ));
            }
        }
    }

    /**
     * Export a single function call parameter.
     * @param string $name The remote function call parameter name.
     * @param string $type The remote function call parameter type.
     * @param array $members The members of a remote function call parameter.
     * @return bool success?
     * @throws \LogicException
     */
    private function setSapRfcParameter($name, $type, $members)
    {
        switch ($type) {
            case 'IMPORT':
                $param = $this->getParam($name, '');
                $result = @saprfc_import($this->function, $name, $param);
                break;
            case 'IMPORT_STRUCT':
                $param = $this->getParam($name, []);
                foreach ($members as $member) {
                    if (!array_key_exists($member, $param)) {
                        $param[$member] = '';
                    }
                }
                $result = @saprfc_import($this->function, $name, $param);
                break;
            case 'TABLE':
                $result = @saprfc_table_init($this->function, $name);
                break;
            case 'EXPORT': //fall through
            case 'EXPORT_STRUCT':
                $result = true;
                break;
            default:
                throw new \LogicException(sprintf(
                    'Unknown type %s in interface of function %s.',
                    $type,
                    $this->getName()
                ));
        }
        return $result;
    }

    /**
     * Import results from the function call.
     * @return array
     * @throws \LogicException
     */
    private function getSaprfcResults()
    {
        $result = [];
        foreach ($this->getRemoteInterface() as $name => $definition) {
            switch ($definition['type']) {
                case 'IMPORT': //fall through
                case 'IMPORT_STRUCT':
                    break;
                case 'EXPORT': //fall through
                case 'EXPORT_STRUCT':
                    $result[$name] = trim(@saprfc_export($this->function, $name));
                    break;
                case 'TABLE':
                    $result[$name] = [];
                    $max = @saprfc_table_rows($this->function, $this->getName());
                    for ($index = 1; $index <= $max; $index++) {
                        $result[$name][] = @saprfc_table_read($this->function, $this->getName(), $index);
                    }
                    break;
                default:
                    throw new \LogicException(sprintf(
                        'Unknown type %s in interface of function %s.',
                        $definition['type'],
                        $this->getName()
                    ));
            }
        }
        return $result;
    }
}
