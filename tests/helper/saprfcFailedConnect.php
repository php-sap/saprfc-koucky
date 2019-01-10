<?php
/**
 * File tests/helper/saprfcSuccessfulConnect.php
 *
 * Define the functions necessary mocking a failed connection attempt.
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
    return false;
}
function saprfc_error()
{
    return 'my error message';
}
