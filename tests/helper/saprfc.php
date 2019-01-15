<?php
/**
 * File tests/helper/saprfcMockFunctionTemplates.php
 *
 * Define the functions used from the saprfc module.
 *
 * @package saprfc-koucky
 * @author  Gregor J.
 * @license MIT
 */
/**
 * Either run tests using this mock of the sapnwrfc class or run the tests with the
 * actual module and an actual SAP system.
 */
if (extension_loaded('saprfc')) {
    throw new \RuntimeException('PHP module saprfc is loaded. Cannot run tests using mockups.');
}

/**
 * Close connection resource.
 * @param mixed $connection Connection ressource.
 */
function saprfc_close(&$connection)
{
    $connection = null;
}

/**
 * Close function resource.
 * @param mixed $function
 */
function saprfc_function_free(&$function)
{
    $function = null;
}

/**
 * Return connection error.
 * @return string Connection error.
 */
function saprfc_error()
{
    $func = \phpsap\IntegrationTests\SapRfcModuleMocks::singleton()->get(__FUNCTION__);
    return $func();
}

/**
 * Get remote function call exception text.
 * @param resource $function
 * @return string
 */
function saprfc_exception($function)
{
    $func = \phpsap\IntegrationTests\SapRfcModuleMocks::singleton()->get(__FUNCTION__);
    return $func($function);
}

/**
 * Open connection.
 * @param array $config
 * @return ressource
 */
function saprfc_open($config)
{
    $func = \phpsap\IntegrationTests\SapRfcModuleMocks::singleton()->get(__FUNCTION__);
    return $func($config);
}

/**
 * Get function call resource.
 * @param ressource $connection Connection ressource.
 * @param string $name Function name.
 * @return ressource
 */
function saprfc_function_discover($connection, $name)
{
    $func = \phpsap\IntegrationTests\SapRfcModuleMocks::singleton()->get(__FUNCTION__);
    return $func($connection, $name);
}

/**
 * Call SAP remote function with all set parameters.
 * @param ressource $function
 * @return int
 */
function saprfc_call_and_receive($function)
{
    $func = \phpsap\IntegrationTests\SapRfcModuleMocks::singleton()->get(__FUNCTION__);
    return $func($function);
}

/**
 * Get remote function call interface defintion.
 * @param resource $function The remote function call resource.
 * @return array
 */
function saprfc_function_interface($function)
{
    $func = \phpsap\IntegrationTests\SapRfcModuleMocks::singleton()->get(__FUNCTION__);
    return $func($function);
}

/**
 * Import remote function call parameters.
 * @param resource $function The remote function call resource.
 * @param string $name The parameter name.
 * @param mixed $value The parameter value.
 * @return bool
 */
function saprfc_import($function, $name, $value)
{
    $func = \phpsap\IntegrationTests\SapRfcModuleMocks::singleton()->get(__FUNCTION__);
    return $func($function, $name, $value);
}

/**
 * Initialize saprfc table.
 * @param resource $function The remote function call resource.
 * @param string $name The parameter name.
 * @return bool
 */
function saprfc_table_init($function, $name)
{
    $func = \phpsap\IntegrationTests\SapRfcModuleMocks::singleton()->get(__FUNCTION__);
    return $func($function, $name);
}

/**
 * Get function call result.
 * @param resource $function The remote function call resource.
 * @param string $name The result parameter name.
 * @return mixed
 */
function saprfc_export($function, $name)
{
    $func = \phpsap\IntegrationTests\SapRfcModuleMocks::singleton()->get(__FUNCTION__);
    return $func($function, $name);
}

/**
 * Get the number of table rows of a remote function call result.
 * @param resource $function The remote function call resource.
 * @param string $name The result parameter name.
 * @return int
 */
function saprfc_table_rows($function, $name)
{
    $func = \phpsap\IntegrationTests\SapRfcModuleMocks::singleton()->get(__FUNCTION__);
    return $func($function, $name);
}

/**
 * Get the row of a table of a remote function call result.
 * @param resource $function The remote function call resource.
 * @param string $name The result parameter name.
 * @param int $index The table row.
 * @return mixed
 */
function saprfc_table_read($function, $name, $index)
{
    $func = \phpsap\IntegrationTests\SapRfcModuleMocks::singleton()->get(__FUNCTION__);
    return $func($function, $name, $index);
}
