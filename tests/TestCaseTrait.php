<?php

namespace tests\phpsap\saprfc;

use phpsap\saprfc\SapRfc;

/**
 * Trait TestCaseTrait
 *
 * Collect methods common to all test cases extending the integration tests.
 *
 * @package tests\phpsap\saprfc
 * @author Gregor J.
 * @license MIT
 */
trait TestCaseTrait
{
    /**
     * Return the name of the class, used for testing.
     * @return string
     */
    public static function getClassName()
    {
        return SapRfc::class;
    }

    /**
     * Get the name of the PHP module.
     * @return string
     */
    public static function getModuleName()
    {
        return 'saprfc';
    }

    /**
     * Get the path to the PHP/SAP configuration file.
     * @return string
     */
    public static function getSapConfigFile()
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'sap.json';
    }

    /**
     * Get the path to the filename containing the SAP RFC module mockups.
     * @return string
     */
    public static function getModuleTemplateFile()
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'helper' . DIRECTORY_SEPARATOR . 'saprfc.php';
    }

    /**
     * Get an array of valid SAP RFC module function or class method names.
     * @return array
     */
    public static function getValidModuleFunctions()
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
}
