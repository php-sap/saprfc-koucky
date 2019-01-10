<?php
/**
 * File tests/helper/saprfcSuccessfulConnect.php
 *
 * Define the functions necessary to mock successfully opening and closing a
 * connection.
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
