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

use phpsap\saprfc\SapRfcConnection;
use tests\phpsap\saprfc\helper\AbstractSaprfcTests;

/**
 * Class tests\phpsap\saprfc\SapRfcConnectionTest
 *
 * Test connection class.
 *
 * @package tests\phpsap\saprfc
 * @author  Gregor J.
 * @license MIT
 */
class SapRfcConnectionTest extends AbstractSaprfcTests
{
    /**
     * Mock SAPRFC functions necessary to perform a successful connection attempt.
     */
    private static function mockSaprfcSuccessfulConnect()
    {
        static::mockSaprfcFunction('saprfc_open', function ($config) {
            if (is_array($config)) {
                return 'SAPRFC CONNECTION RESOURCE MOCK';
            }
            return false;
        });
        static::mockSaprfcFunction('saprfc_close', function (&$connection) {
            $connection = null;
        });
    }

    /**
     * Run a successful connection attempt.
     */
    public function testSaprfcSuccessfulConnect()
    {
        if (!extension_loaded('saprfc')) {
            //load functions mocking saprfc module functions
            static::mockSaprfcSuccessfulConnect();
            //load a bogus config
            $config = static::getOfflineSapConfig();
        } else {
            //load a valid config
            $config = static::getOnlineSapConfig('sap');
        }
        $connection = new SapRfcConnection($config);
        static::assertFalse($connection->isConnected());
        $connection->connect();
        static::assertTrue($connection->isConnected());
        $connection->connect();
        static::assertTrue($connection->isConnected());
        $connection->close();
        static::assertFalse($connection->isConnected());
    }

    /**
     * Mock SAPRFC functions necessary to perform a failed connection attempt.
     */
    private static function mockSaprfcFailedConnect()
    {
        static::mockSaprfcFunction('saprfc_open', function ($config) {
            return false;
        });
        static::mockSaprfcFunction('saprfc_error', function () {
            return 'my error message';
        });
    }

    /**
     * Run a failed connection attempt.
     * @expectedException \phpsap\exceptions\ConnectionFailedException
     */
    public function testSaprfcFailedConnect()
    {
        if (!extension_loaded('saprfc')) {
            //load functions mocking saprfc module functions
            static::mockSaprfcFailedConnect();
            //load a bogus config
            $config = static::getOfflineSapConfig();
        } else {
            //load a valid config
            $config = static::getOnlineSapConfig('sap_invalid_ashost');
        }
        $connection = new SapRfcConnection($config);
        $connection->connect();
    }

    /**
     * Mock SAPRFC functions necessary to perform a successful connection ping.
     */
    private static function mockSaprfcSuccessfulPing()
    {
        static::mockSaprfcFunction('saprfc_open', function ($config) {
            if (is_array($config)) {
                return 'SAPRFC CONNECTION';
            }
            return false;
        });
        static::mockSaprfcFunction('saprfc_close', function (&$connection) {
            $connection = null;
        });
        static::mockSaprfcFunction('saprfc_function_discover', function ($connection, $name) {
            if ($connection === 'SAPRFC CONNECTION' && $name === 'RFC_PING') {
                return 'SAPRFC PING';
            }
            return false;
        });
        static::mockSaprfcFunction('saprfc_call_and_receive', function ($function) {
            if ($function === 'SAPRFC PING') {
                return 0;
            }
            return 1;
        });
        static::mockSaprfcFunction('saprfc_function_free', function (&$function) {
            $function = null;
        });
        static::mockSaprfcFunction('saprfc_function_interface', function () {
            return [];
        });
    }

    /**
     * Successfully ping a connection.
     */
    public function testSaprfcSuccessfulPing()
    {
        if (!extension_loaded('saprfc')) {
            //load functions mocking saprfc module functions
            static::mockSaprfcSuccessfulPing();
            //load a bogus config
            $config = static::getOfflineSapConfig();
        } else {
            //load a valid config
            $config = static::getOnlineSapConfig('sap');
        }
        $connection = new SapRfcConnection($config);
        $result = $connection->ping();
        static::assertTrue($result);
    }

    /**
     * Mock SAPRFC functions necessary to perform a failed connection ping.
     */
    public static function mockSaprfcFailedPing()
    {
        static::mockSaprfcFunction('saprfc_open', function ($config) {
            if (is_array($config)) {
                return 'SAPRFC CONNECTION';
            }
            return false;
        });
        static::mockSaprfcFunction('saprfc_close', function (&$connection) {
            $connection = null;
        });
        static::mockSaprfcFunction('saprfc_function_discover', function ($connection, $name) {
            if ($connection === 'SAPRFC CONNECTION' && $name === 'RFC_PING') {
                return 'SAPRFC PING';
            }
            return false;
        });
        static::mockSaprfcFunction('saprfc_call_and_receive', function ($function) {
            if ($function === 'SAPRFC PING') {
                return 1;
            }
            return 0;
        });
        static::mockSaprfcFunction('saprfc_function_free', function (&$function) {
            $function = null;
        });
        static::mockSaprfcFunction('saprfc_function_interface', function () {
            return [];
        });
        static::mockSaprfcFunction('saprfc_exception', function ($function) {
            return sprintf('%s EXCEPTION', $function);
        });
    }

    /**
     * Fail to ping a connection.
     */
    public function testSaprfcFailedPing()
    {
        if (!extension_loaded('saprfc')) {
            //load functions mocking saprfc module functions
            static::mockSaprfcFailedPing();
            //load a bogus config
            $config = static::getOfflineSapConfig();
        } else {
            static::markTestSkipped('Cannot test a failing ping with saprfc loaded.');
        }
        $connection = new SapRfcConnection($config);
        $result = $connection->ping();
        static::assertFalse($result);
    }

    /**
     * Try to clean up the error output file of the saprfc module, that has been
     * created during online tests.
     */
    public function __destruct()
    {
        $devRfc = realpath(
            __DIR__
            .DIRECTORY_SEPARATOR
            .'..'
            .DIRECTORY_SEPARATOR
            .'dev_rfc.trc'
        );
        if (file_exists($devRfc)) {
            @unlink($devRfc);
        }
    }
}
