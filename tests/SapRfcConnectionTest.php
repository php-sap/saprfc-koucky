<?php

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
        static::mock('saprfc_open', static function ($config) {
            if (is_array($config)) {
                return 'SAPRFC CONNECTION RESOURCE MOCK';
            }
            return false;
        });
        static::mock('saprfc_close', static function (&$connection) {
            $connection = null;
        });
        static::mock('saprfc_function_discover', function ($connection, $name) {
            if ($connection === 'SAPRFC CONNECTION RESOURCE MOCK' && $name === 'RFC_PING') {
                return 'SAPRFC PING';
            }
            return false;
        });
        static::mock('saprfc_function_free', function (&$function) {
            $function = null;
        });
        static::mock('saprfc_function_interface', function () {
            return [];
        });
    }

    /**
     * Mock the SAP RFC module for a failed connection attempt.
     */
    protected function mockConnectionFailed()
    {
        static::mock('saprfc_open', static function ($config) {
            unset($config);
            return false;
        });
        static::mock('saprfc_error', static function () {
            return 'my error message';
        });
    }
}
