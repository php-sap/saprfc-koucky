<?php

namespace phpsap\saprfc;

use phpsap\classes\AbstractFunction;
use phpsap\classes\Api\RemoteApi;
use phpsap\exceptions\ArrayElementMissingException;
use phpsap\exceptions\ConnectionFailedException;
use phpsap\exceptions\FunctionCallException;
use phpsap\exceptions\IncompleteConfigException;
use phpsap\exceptions\SapLogicException;
use phpsap\exceptions\UnknownFunctionException;
use phpsap\interfaces\exceptions\IIncompleteConfigException;
use phpsap\saprfc\Traits\ApiTrait;
use phpsap\saprfc\Traits\ConfigTrait;

/**
 * Class phpsap\saprfc\SapRfc
 *
 * PHP/SAP class abstracting SAP remote function calls using Eduard Kouckys
 * saprfc module.
 *
 * @package phpsap\saprfc
 * @author Gregor J.
 * @license MIT
 */
class SapRfc extends AbstractFunction
{
    /**
     * Module specific configuration generation.
     */
    use ConfigTrait;

    /**
     * Module specific API creation.
     */
    use ApiTrait;

    /**
     * @var resource
     */
    private $connection;

    /**
     * @var resource
     */
    private $function;

    /**
     * Create a remote function call resource.
     * @return resource
     * @throws ConnectionFailedException
     * @throws IncompleteConfigException
     * @throws UnknownFunctionException
     */
    protected function getFunction()
    {
        if ($this->function === null) {
            /**
             * Create a new function resource.
             */
            $this->function = @saprfc_function_discover(
                $this->getConnection(),
                $this->getName()
            );
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
     * Open a connection in case it hasn't been done yet and return the
     * connection resource.
     * @return resource
     * @throws ConnectionFailedException
     * @throws IncompleteConfigException
     */
    protected function getConnection()
    {
        if ($this->connection === null) {
            /**
             * In case the is no configuration, throw an exception.
             */
            if (($config = $this->getConfiguration()) === null) {
                throw new IncompleteConfigException(
                    'Configuration is missing!'
                );
            }
            /**
             * Catch generic IIncompleteConfigException interface and throw the
             * actual exception class of this repository.
             */
            try {
                $moduleConfig = $this->getModuleConfig($config);
            } catch (IIncompleteConfigException $exception) {
                throw new IncompleteConfigException(
                    $exception->getMessage(),
                    $exception->getCode(),
                    $exception
                );
            }
            /**
             * Create a new connection resource.
             */
            $this->connection = @saprfc_open($moduleConfig);
            /**
             * In case the connection couldn't be opened, throw an exception.
             */
            if ($this->connection === false) {
                $this->connection = null;
                throw new ConnectionFailedException(sprintf(
                    'Connection creation failed: %s',
                    @saprfc_error()
                ));
            }
        }
        return $this->connection;
    }

    /**
     * Module specific destructor.
     */
    public function __destruct()
    {
        if ($this->function !== null) {
            @saprfc_function_free($this->function);
            $this->function = null;
        }
        if ($this->connection !== null) {
            @saprfc_close($this->connection);
            $this->connection = null;
        }
    }

    /**
     * Connect to the SAP remote system and retrieve the API of the SAP remote
     * function. This ignores any API settings in this class.
     * @return RemoteApi
     * @throws ConnectionFailedException
     * @throws IncompleteConfigException
     * @throws UnknownFunctionException
     * @throws SapLogicException
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
     * @throws ConnectionFailedException
     * @throws IncompleteConfigException
     * @throws UnknownFunctionException
     */
    protected function saprfcFunctionInterface()
    {
        $definitions = @saprfc_function_interface($this->getFunction());
        if ($definitions === false) {
            throw new ConnectionFailedException(
                'Cannot query remote function API!'
            );
        }
        return $definitions;
    }

    /**
     * Invoke the SAP remote function call with all parameters.
     * Attention: A configuration is necessary to invoke a SAP remote function
     * call!
     * @return array
     * @throws ArrayElementMissingException
     * @throws ConnectionFailedException
     * @throws FunctionCallException
     * @throws IncompleteConfigException
     * @throws UnknownFunctionException
     */
    public function invoke()
    {
        $this->setSapRfcInputValues($this->getApi()->getInputValues(), $this->getParams());
        $this->setSapRfcTables($this->getApi()->getTables(), $this->getParams());
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
     * Set all input values.
     * @param array $inputs The array of input parameters to set.
     * @param array $params
     * @return void
     * @throws ConnectionFailedException
     * @throws FunctionCallException
     * @throws IncompleteConfigException
     * @throws UnknownFunctionException
     */
    private function setSapRfcInputValues($inputs, $params)
    {
        foreach ($inputs as $input) {
            $key = $input->getName();
            if (array_key_exists($key, $params)) {
                $value = $params[$key];
                if (!@saprfc_import($this->getFunction(), $key, $value)) {
                    throw new FunctionCallException(sprintf(
                        'Function call %s failed: Assigning param %s failed. Expected type %s, actual type %s.',
                        $this->getName(),
                        $key,
                        $input->getType(),
                        gettype($value)
                    ));
                }
            } elseif (!$input->isOptional()) {
                throw new FunctionCallException(sprintf(
                    'Missing parameter \'%s\' for function call \'%s\'!',
                    $key,
                    $this->getName()
                ));
            }
        }
    }

    /**
     * Initializes the table parameters of the remote function call.
     * @param array $tables The array of table parameters to initialize.
     * @param array $params
     * @return void
     * @throws ConnectionFailedException
     * @throws FunctionCallException In case the initialization fails.
     * @throws IncompleteConfigException
     * @throws UnknownFunctionException
     */
    private function setSapRfcTables($tables, $params)
    {
        foreach ($tables as $table) {
            $key = $table->getName();
            /**
             * Initialize each table.
             */
            if (!@saprfc_table_init($this->getFunction(), $key)) {
                throw new FunctionCallException(sprintf(
                    'Initializing table %s for function %s failed!',
                    $key,
                    $this->getName()
                ));
            }
            /**
             * Fill the prepared table in case there are values in the parameters.
             */
            if (
                array_key_exists($key, $params)
                && is_array($params[$key])
                && count($params[$key]) > 0
            ) {
                foreach ($params[$key] as $number => $row) {
                    if (!@saprfc_table_append($this->getFunction(), $key, $row)) {
                        throw new FunctionCallException(sprintf(
                            'Adding row #%u to table %s for function %s failed!',
                            $number,
                            $key,
                            $this->getName()
                        ));
                    }
                }
            }
        }
    }

    /**
     * Import results from the function call.
     * @return array
     * @throws ConnectionFailedException
     * @throws IncompleteConfigException
     * @throws UnknownFunctionException
     * @throws ArrayElementMissingException
     */
    private function getSaprfcResults()
    {
        $result = [];
        foreach ($this->getApi()->getOutputValues() as $output) {
            $key = $output->getName();
            $value = @saprfc_export($this->getFunction(), $key);
            $result[$key] = $output->cast($value);
            unset($key, $value);
        }
        foreach ($this->getApi()->getTables() as $table) {
            $key = $table->getName();
            $rows = [];
            $max = @saprfc_table_rows($this->getFunction(), $key);
            for ($index = 1; $index <= $max; $index++) {
                $rows[] = @saprfc_table_read($this->getFunction(), $key, $index);
            }
            $result[$key] = $table->cast($rows);
            unset($key, $rows, $max, $index);
        }
        return $result;
    }
}
