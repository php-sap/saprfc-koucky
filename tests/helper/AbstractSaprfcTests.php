<?php
/**
 * File tests/helper/AbstractSaprfcTests.php
 *
 * Helper class defining methods all tests will need.
 *
 * @package saprfc-koucky
 * @author  Gregor J.
 * @license MIT
 */

namespace tests\phpsap\saprfc\helper;

use phpsap\saprfc\SapRfcConfigA;

/**
 * Class tests\phpsap\saprfc\AbstractSaprfcTests
 *
 * Helper class defining methods all tests will need.
 *
 * @package Tests\phpsap\saprfc
 * @author  Gregor J.
 * @license MIT
 */
class AbstractSaprfcTests extends \PHPUnit_Framework_TestCase
{
    /**
     * Create a IConfig instance.
     * @return \phpsap\saprfc\SapRfcConfigA
     */
    protected static function getOfflineSapConfig()
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
    protected static function getOnlineSapConfig($name)
    {
        $configFile = __DIR__ . DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR
                      . strtolower($name) . '.json';
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
     * @param $name
     * @param $function
     */
    protected static function mockSaprfcFunction($name, $function)
    {
        SaprfcMockFunctions::singleton()->mock($name, $function);
    }
}
