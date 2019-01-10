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

use phpsap\saprfc\SapRfcConfigA;
use phpsap\saprfc\SapRfcConnection;

/**
 * Class tests\phpsap\saprfc\SapRfcConnectionTest
 *
 * Test connection class.
 *
 * @package tests\phpsap\saprfc
 * @author  Gregor J.
 * @license MIT
 * @runTestsInSeparateProcesses
 */
class SapRfcConnectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Create a IConfig instance.
     * @return \phpsap\saprfc\SapRfcConfigA
     */
    private static function getOfflineSapConfig()
    {
        $config = '{
            "ashost": "sap.example.com",
            "sysnr": "001",
            "client": "01",
            "user": "username",
            "passwd": "password"
        }';
        return new SapRfcConfigA($config);
    }

    /**
     * Create a IConfig instance.
     * @param string $name Configuration file name (without .json).
     * @return \phpsap\saprfc\SapRfcConfigA
     */
    private static function getOnlineSapConfig($name)
    {
        $configFile = __DIR__ . DIRECTORY_SEPARATOR
                      . 'config'. DIRECTORY_SEPARATOR
                      . strtolower($name)  .'.json';
        if (file_exists($configFile) !== true) {
            throw new \RuntimeException(sprintf('Cannot find config file %s!', $configFile));
        }

        if (($configJson = file_get_contents($configFile)) === false) {
            throw new \RuntimeException(sprintf('Cannot read from config file %s!', $configFile));
        }

        if (($configArr = json_decode($configJson, true)) === null) {
            throw new \RuntimeException(sprintf('Invalid JSON format in config file %s!', $configFile));
        }
        return new SapRfcConfigA($configArr);
    }

    /**
     * Load functions mocking saprfc module functions and test their expected
     * behavior for the SuccessfulConnect test.
     */
    private static function prepareOfflineSuccessfulConnect()
    {
        //load functions mocking saprfc module functions
        require_once __DIR__.DIRECTORY_SEPARATOR.'helper'.DIRECTORY_SEPARATOR.'saprfcSuccessfulConnect.php';
        //test the existance of the functions and their expected return values.
        static::assertTrue(function_exists('saprfc_open'));
        static::assertEquals('SAPRFC CONNECTION', saprfc_open([]));
        static::assertTrue(function_exists('saprfc_close'));
        $testMe = true;
        saprfc_close($testMe);
        static::assertNull($testMe);
    }

    /**
     * Run a successful connection attempt.
     */
    public function testSaprfcSuccessfulConnect()
    {
        if (!extension_loaded('saprfc')) {
            //load functions mocking saprfc module functions
            static::prepareOfflineSuccessfulConnect();
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
     * Load functions mocking saprfc module functions and test their expected
     * behavior for the SaprfcFailedConnect test.
     */
    private static function prepareOfflineSaprfcFailedConnect()
    {
        //load functions mocking saprfc module functions
        require_once __DIR__.DIRECTORY_SEPARATOR.'helper'.DIRECTORY_SEPARATOR.'saprfcFailedConnect.php';
        //test the existance of the functions and their expected return values.
        static::assertTrue(function_exists('saprfc_open'));
        static::assertFalse(saprfc_open([]));
        static::assertTrue(function_exists('saprfc_error'));
        static::assertEquals('my error message', saprfc_error());
    }

    /**
     * Run a failed connection attempt.
     * @expectedException \phpsap\exceptions\ConnectionFailedException
     */
    public function testSaprfcFailedConnect()
    {
        if (!extension_loaded('saprfc')) {
            //load functions mocking saprfc module functions
            static::prepareOfflineSaprfcFailedConnect();
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
     * Load functions mocking saprfc module functions and test their expected
     * behavior for the SaprfcSuccessfulPing test.
     */
    private static function prepareOfflineSaprfcSuccessfulPing()
    {
        //load functions mocking saprfc module functions
        require_once __DIR__.DIRECTORY_SEPARATOR.'helper'.DIRECTORY_SEPARATOR.'saprfcSuccessfulPing.php';
        //test the existance of the functions and their expected return values.
        static::assertTrue(function_exists('saprfc_open'));
        $connection = saprfc_open([]);
        static::assertEquals('SAPRFC CONNECTION', $connection);
        static::assertTrue(function_exists('saprfc_function_discover'));
        $ping = saprfc_function_discover($connection, 'RFC_PING');
        static::assertEquals('SAPRFC PING', $ping);
        static::assertTrue(function_exists('saprfc_call_and_receive'));
        $result = saprfc_call_and_receive($ping);
        static::assertSame(0, $result);
        static::assertTrue(function_exists('saprfc_function_free'));
        saprfc_function_free($ping);
        static::assertNull($ping);
        static::assertTrue(function_exists('saprfc_function_interface'));
        $fcinterface = saprfc_function_interface();
        static::assertSame([], $fcinterface);
        unset($connection, $ping, $result, $fcinterface);
    }

    /**
     * Successfully ping a connection.
     * @group offline
     */
    public function testSaprfcSuccessfulPing()
    {
        if (!extension_loaded('saprfc')) {
            //load functions mocking saprfc module functions
            static::prepareOfflineSaprfcSuccessfulPing();
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
     * Load functions mocking saprfc module functions and test their expected
     * behavior for the SaprfcFailedPing test.
     */
    public static function prepareOfflineSaprfcFailedPing()
    {
        //load functions mocking saprfc module functions
        require_once __DIR__.DIRECTORY_SEPARATOR.'helper'.DIRECTORY_SEPARATOR.'saprfcFailedPing.php';
        //test the existance of the functions and their expected return values.
        static::assertTrue(function_exists('saprfc_open'));
        $connection = saprfc_open([]);
        static::assertEquals('SAPRFC CONNECTION', $connection);
        static::assertTrue(function_exists('saprfc_function_discover'));
        $ping = saprfc_function_discover($connection, 'RFC_PING');
        static::assertEquals('SAPRFC PING', $ping);
        static::assertTrue(function_exists('saprfc_call_and_receive'));
        $result = saprfc_call_and_receive($ping);
        //this time ping should fail
        static::assertSame(1, $result);
        static::assertTrue(function_exists('saprfc_function_free'));
        saprfc_function_free($ping);
        static::assertNull($ping);
        static::assertTrue(function_exists('saprfc_function_interface'));
        $fcinterface = saprfc_function_interface();
        static::assertSame([], $fcinterface);
        unset($connection, $ping, $result, $fcinterface);
    }

    /**
     * Fail to ping a connection.
     */
    public function testSaprfcFailedPing()
    {
        if (!extension_loaded('saprfc')) {
            //load functions mocking saprfc module functions
            static::prepareOfflineSaprfcFailedPing();
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
