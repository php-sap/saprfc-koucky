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
     * SAP remote function ressource.
     * @var mixed
     */
    private $function;

    /**
     * SAP remote function interface.
     * @var mixed
     */
    private $functionInterface;

    /**
     * Invoke the prepared function call.
     * @return array
     * @throws \phpsap\exceptions\FunctionCallException
     */
    protected function execute()
    {
        $this->setSaprfcParameters();
        $result = @saprfc_call_and_receive($this->getFunction());
        if ($result !== 0) {
            throw new FunctionCallException(sprintf(
                'Function call %s failed: %s',
                $this->getName(),
                @saprfc_exception($this->getFunction())
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
     * All parameter names will be converted to upper case automagically.
     * @param string                           $name
     * @param array|string|float|int|bool|null $value
     * @return \phpsap\interfaces\IFunction $this
     */
    public function setParam($name, $value)
    {
        return parent::setParam(
            strtoupper($name),
            $value
        );
    }

    /**
     * Create a remote function call ressource.
     * @return mixed
     * @throws \phpsap\exceptions\UnknownFunctionException
     */
    protected function getFunction()
    {
        if ($this->function === null) {
            $this->function = @saprfc_function_discover($this->connection, $this->getName());
            if ($this->function === false) {
                $this->function = null;
                throw new UnknownFunctionException(sprintf(
                    'Unknown function %s: %s',
                    $this->getName(),
                    @saprfc_error()
                ));
            }
        }
        return $this->function;
    }

    /**
     * Retrieve the interface of the remote function.
     * @return array
     */
    protected function getRemoteInterface()
    {
        if ($this->functionInterface === null) {
            $this->functionInterface = [];
            foreach ($this->saprfcFunctionInterface() as $definition) {
                //parameter/result name
                $name = strtoupper($definition['name']);
                //parameter/result members
                $members = $definition['def'];
                //parameter/result type
                $type = $definition['type'];
                if ($type !== 'TABLE'
                    && isset($members[0]['name'])
                    && $members[0]['name'] !== ''
                ) {
                    $type.= '_STRUCT';
                }
                //set definition
                $this->functionInterface[$name] = [
                    'type' => $type,
                    'members' => $members
                ];
            }
        }
        return $this->functionInterface;
    }

    /**
     * Get remote function call interface definition.
     * @return array
     */
    private function saprfcFunctionInterface()
    {
        $definitions = @saprfc_function_interface($this->getFunction());
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
                    'Assigning param %s expected type %s, actual type %s, to function %s failed.',
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
     */
    private function setSapRfcParameter($name, $type, $members)
    {
        switch ($type) {
            case 'IMPORT':
                $param = $this->getParam($name, '');
                $result = @saprfc_import($this->getFunction(), $name, $param);
                break;
            case 'IMPORT_STRUCT':
                $param = $this->getParam($name, []);
                foreach ($members as $member) {
                    if (!array_key_exists($member, $param)) {
                        $param[$member] = '';
                    }
                }
                $result = @saprfc_import($this->getFunction(), $name, $param);
                break;
            case 'TABLE':
                $result = @saprfc_table_init($this->getFunction(), $name);
                break;
            case 'EXPORT': //fall through
            case 'EXPORT_STRUCT':
                $result = true;
                break;
            default:
                throw new \LogicException(sprintf(
                    'Unkown type %s in interface of function %s.',
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
                    $result[$name] = trim(@saprfc_export($this->getFunction(), $name));
                    break;
                case 'TABLE':
                    $result[$name] = [];
                    $max = @saprfc_table_rows($this->getFunction(), $this->getName());
                    for ($index = 1; $index <= $max; $index++) {
                        $result[$name][] = @saprfc_table_read($this->getFunction(), $this->getName(), $index);
                    }
                    break;
                default:
                    throw new \LogicException(sprintf(
                        'Unkown type %s in interface of function %s.',
                        $definition['type'],
                        $this->getName()
                    ));
            }
        }
        return $result;
    }
}
