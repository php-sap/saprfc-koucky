<?php

namespace tests\phpsap\saprfc;

use phpsap\classes\Config\ConfigTypeA;
use phpsap\interfaces\Config\IConfiguration;
use phpsap\saprfc\SapRfcConnection;

/**
 * Trait tests\phpsap\saprfc\SapRfcTestCaseTrait
 *
 * Implement methods of phpsap\IntegrationTests\AbstractTestCase
 *
 * @package tests\phpsap\saprfc
 * @author Gregor J.
 * @license MIT
 */
trait SapRfcTestCaseTrait
{
    /**
     * Get the name of the PHP module.
     * @return string
     */
    public function getModuleName()
    {
        return 'saprfc';
    }

    /**
     * Get the path to the PHP/SAP configuration file.
     * @return string
     */
    public function getSapConfigFile()
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'sap.json';
    }

    /**
     * Get the path to the filename containing the SAP RFC module mockups.
     * @return string
     */
    public function getModuleTemplateFile()
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'helper' . DIRECTORY_SEPARATOR . 'saprfc.php';
    }

    /**
     * Get an array of valid SAP RFC module function or class method names.
     * @return array
     */
    public function getValidModuleFunctions()
    {
        return [
            'saprfc_close',
            'saprfc_function_free',
            'saprfc_error',
            'saprfc_exception',
            'saprfc_open',
            'saprfc_function_discover',
            'saprfc_call_and_receive',
            'saprfc_function_interface',
            'saprfc_import',
            'saprfc_table_init',
            'saprfc_table_append',
            'saprfc_export',
            'saprfc_table_rows',
            'saprfc_table_read'
        ];
    }

    /**
     * Create a new instance of a PHP/SAP connection class.
     * @param IConfiguration|null $config The PHP/SAP configuration. Default: null
     * @return SapRfcConnection
     * @throws \InvalidArgumentException
     */
    public function newConnection(IConfiguration $config = null)
    {
        if ($config === null) {
            return new SapRfcConnection(new ConfigTypeA());
        }
        return new SapRfcConnection($config);
    }

    /**
     * Clean up trace files after tests.
     */
    public function __destruct()
    {
        $traceFile = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'dev_rfc.trc';
        if (file_exists($traceFile)) {
            unlink($traceFile);
        }
    }
}
