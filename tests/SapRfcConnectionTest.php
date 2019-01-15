<?php
/**
 * File tests/SapRfcConnectionTest.php
 *
 * Test connection class.
 *
 * @package saprfc-koucky
 * @author  Gregor J.
 * @license MIT
 */

namespace tests\phpsap\saprfc;

use phpsap\IntegrationTests\AbstractConnectionTestCase;

/**
 * Class tests\phpsap\saprfc\SapRfcConnectionTest
 *
 * Test connection class.
 *
 * @package tests\phpsap\saprfc
 * @author  Gregor J.
 * @license MIT
 */
class SapRfcConnectionTest extends AbstractConnectionTestCase
{
    /**
     * Implements methods of phpsap\IntegrationTests\AbstractTestCase
     */
    use SapRfcTestCaseTrait;

    /**
     * Mock the SAP RFC module for a successful connection attempt.
     */
    protected function mockSuccessfulConnect()
    {
        static::mock('saprfc_open', function ($config) {
            if (is_array($config)) {
                return 'SAPRFC CONNECTION RESOURCE MOCK';
            }
            return false;
        });
        static::mock('saprfc_close', function (&$connection) {
            $connection = null;
        });
    }

    /**
     * Mock the SAP RFC module for a failed connection attempt.
     */
    protected function mockFailedConnect()
    {
        static::mock('saprfc_open', function ($config) {
            return false;
        });
        static::mock('saprfc_error', function () {
            return 'my error message';
        });
    }

    /**
     * Mock the SAP RFC module for a successful attempt to ping a connection.
     */
    protected function mockSuccessfulPing()
    {
        static::mock('saprfc_open', function ($config) {
            if (is_array($config)) {
                return 'SAPRFC CONNECTION';
            }
            return false;
        });
        static::mock('saprfc_close', function (&$connection) {
            $connection = null;
        });
        static::mock('saprfc_function_discover', function ($connection, $name) {
            if ($connection === 'SAPRFC CONNECTION' && $name === 'RFC_PING') {
                return 'SAPRFC PING';
            }
            return false;
        });
        static::mock('saprfc_call_and_receive', function ($function) {
            if ($function === 'SAPRFC PING') {
                return 0;
            }
            return 1;
        });
        static::mock('saprfc_function_free', function (&$function) {
            $function = null;
        });
        static::mock('saprfc_function_interface', function () {
            return [];
        });
    }

    /**
     * Mock the SAP RFC module for a failed attempt to ping a connection.
     */
    protected function mockFailedPing()
    {
        static::mock('saprfc_open', function ($config) {
            if (is_array($config)) {
                return 'SAPRFC CONNECTION';
            }
            return false;
        });
        static::mock('saprfc_close', function (&$connection) {
            $connection = null;
        });
        static::mock('saprfc_function_discover', function ($connection, $name) {
            if ($connection === 'SAPRFC CONNECTION' && $name === 'RFC_PING') {
                return 'SAPRFC PING';
            }
            return false;
        });
        static::mock('saprfc_call_and_receive', function ($function) {
            if ($function === 'SAPRFC PING') {
                return 1;
            }
            return 0;
        });
        static::mock('saprfc_function_free', function (&$function) {
            $function = null;
        });
        static::mock('saprfc_function_interface', function () {
            return [];
        });
        static::mock('saprfc_exception', function ($function) {
            return sprintf('%s EXCEPTION', $function);
        });
    }
}
