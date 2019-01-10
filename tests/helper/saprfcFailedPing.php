<?php
/**
 * File tests/helper/saprfcFailedConnect.php
 *
 * Define the functions necessary to mock a failure in pinging a SAP system.
 *
 * @package saprfc-koucky
 * @author  Gregor J.
 * @license MIT
 */
if (extension_loaded('saprfc')) {
    throw new \RuntimeException('Extension saprfc is loaded. Cannot run test.');
}
function saprfc_open($config)
{
    if (is_array($config)) {
        return 'SAPRFC CONNECTION';
    }
    return false;
}
function saprfc_close(&$connection)
{
    $connection = null;
}
function saprfc_function_discover(&$connection, $name)
{
    if ($connection === 'SAPRFC CONNECTION' && $name === 'RFC_PING') {
        return 'SAPRFC PING';
    }
    return false;
}
function saprfc_call_and_receive(&$function)
{
    if ($function === 'SAPRFC PING') {
        return 1;
    }
    return 0;
}
function saprfc_function_free(&$function)
{
    $function = null;
}
function saprfc_function_interface()
{
    return [];
}
function saprfc_exception(&$function)
{
    return sprintf('%s EXCEPTION', $function);
}
