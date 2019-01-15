<?php
/**
 * File tests/SapRfcFunctionTest.php
 *
 * Test function class.
 *
 * @package saprfc-koucky
 * @author Gregor J.
 * @license MIT
 */

namespace tests\phpsap\saprfc;

use phpsap\IntegrationTests\AbstractFunctionTestCase;

/**
 * Class tests\phpsap\saprfc\SapRfcFunctionTest
 *
 * Test function class.
 *
 * @package tests\phpsap\saprfc
 * @author Gregor J.
 * @license MIT
 */
class SapRfcFunctionTest extends AbstractFunctionTestCase
{
    /**
     * Implements methods of phpsap\IntegrationTests\AbstractTestCase
     */
    use SapRfcTestCaseTrait;

    /**
     * Mock the SAP RFC module for a successful SAP remote function call.
     */
    protected function mockSuccessfulFunctionCall()
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
            return false;
        });
    }

    /**
     * Mock the SAP RFC module for an unknown function call exception.
     */
    protected function mockUnknownFunctionException()
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
            if ($connection === 'SAPRFC CONNECTION' && $name === 'RFC_PINGG') {
                return false;
            }
            return $name;
        });
        static::mock('saprfc_error', function () {
            return 'function RFC_PINGG not found';
        });
        static::mock('saprfc_function_free', function (&$function) {
            $function = null;
        });
    }

    /**
     * Mock the SAP RFC module for a successful SAP remote function call with
     * parameters and results.
     */
    protected function mockRemoteFunctionCallWithParametersAndResults()
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
            if ($connection === 'SAPRFC CONNECTION' && $name === 'Z_MC_GET_DATE_TIME') {
                return 'SAPRFC Z_MC_GET_DATE_TIME';
            }
            return false;
        });
        static::mock('saprfc_function_interface', function ($function) {
            if ($function === 'SAPRFC Z_MC_GET_DATE_TIME') {
                return [
                    0 => [
                        'name' => 'EV_FRIDAY',
                        'type' => 'EXPORT',
                        'optional' => 0,
                        'def' => [
                            0 => [
                                'name' => '',
                                'abap' => 'D',
                                'len' => 8,
                                'dec' => 0,
                                'offset' => 0,
                            ],
                        ],
                    ],
                    1 => [
                        'name' => 'EV_FRIDAY_LAST',
                        'type' => 'EXPORT',
                        'optional' => 0,
                        'def' => [
                            0 => [
                                'name' => '',
                                'abap' => 'D',
                                'len' => 8,
                                'dec' => 0,
                                'offset' => 0,
                            ],
                        ],
                    ],
                    2 => [
                        'name' => 'EV_FRIDAY_NEXT',
                        'type' => 'EXPORT',
                        'optional' => 0,
                        'def' => [
                            0 => [
                                'name' => '',
                                'abap' => 'D',
                                'len' => 8,
                                'dec' => 0,
                                'offset' => 0,
                            ],
                        ],
                    ],
                    3 => [
                        'name' => 'EV_FRITXT',
                        'type' => 'EXPORT',
                        'optional' => 0,
                        'def' => [
                            0 => [
                                'name' => '',
                                'abap' => 'C',
                                'len' => 15,
                                'dec' => 0,
                                'offset' => 0,
                            ],
                        ],
                    ],
                    4 => [
                        'name' => 'EV_MONDAY',
                        'type' => 'EXPORT',
                        'optional' => 0,
                        'def' =>
                            [
                                0 =>
                                    [
                                        'name' => '',
                                        'abap' => 'D',
                                        'len' => 8,
                                        'dec' => 0,
                                        'offset' => 0,
                                    ],
                            ],
                    ],
                    5 => [
                        'name' => 'EV_MONDAY_LAST',
                        'type' => 'EXPORT',
                        'optional' => 0,
                        'def' =>
                            [
                                0 =>
                                    [
                                        'name' => '',
                                        'abap' => 'D',
                                        'len' => 8,
                                        'dec' => 0,
                                        'offset' => 0,
                                    ],
                            ],
                    ],
                    6 => [
                        'name' => 'EV_MONDAY_NEXT',
                        'type' => 'EXPORT',
                        'optional' => 0,
                        'def' =>
                            [
                                0 =>
                                    [
                                        'name' => '',
                                        'abap' => 'D',
                                        'len' => 8,
                                        'dec' => 0,
                                        'offset' => 0,
                                    ],
                            ],
                    ],
                    7 => [
                        'name' => 'EV_MONTH',
                        'type' => 'EXPORT',
                        'optional' => 0,
                        'def' =>
                            [
                                0 =>
                                    [
                                        'name' => '',
                                        'abap' => 'N',
                                        'len' => 2,
                                        'dec' => 0,
                                        'offset' => 0,
                                    ],
                            ],
                    ],
                    8 => [
                        'name' => 'EV_MONTH_LAST_DAY',
                        'type' => 'EXPORT',
                        'optional' => 0,
                        'def' =>
                            [
                                0 =>
                                    [
                                        'name' => '',
                                        'abap' => 'D',
                                        'len' => 8,
                                        'dec' => 0,
                                        'offset' => 0,
                                    ],
                            ],
                    ],
                    9 => [
                        'name' => 'EV_MONTXT',
                        'type' => 'EXPORT',
                        'optional' => 0,
                        'def' =>
                            [
                                0 =>
                                    [
                                        'name' => '',
                                        'abap' => 'C',
                                        'len' => 15,
                                        'dec' => 0,
                                        'offset' => 0,
                                    ],
                            ],
                    ],
                    10 => [
                        'name' => 'EV_TIMESTAMP',
                        'type' => 'EXPORT',
                        'optional' => 0,
                        'def' =>
                            [
                                0 =>
                                    [
                                        'name' => '',
                                        'abap' => 'C',
                                        'len' => 14,
                                        'dec' => 0,
                                        'offset' => 0,
                                    ],
                            ],
                    ],
                    11 => [
                        'name' => 'EV_WEEK',
                        'type' => 'EXPORT',
                        'optional' => 0,
                        'def' =>
                            [
                                0 =>
                                    [
                                        'name' => '',
                                        'abap' => 'N',
                                        'len' => 6,
                                        'dec' => 0,
                                        'offset' => 0,
                                    ],
                            ],
                    ],
                    12 => [
                        'name' => 'EV_WEEK_LAST',
                        'type' => 'EXPORT',
                        'optional' => 0,
                        'def' =>
                            [
                                0 =>
                                    [
                                        'name' => '',
                                        'abap' => 'N',
                                        'len' => 6,
                                        'dec' => 0,
                                        'offset' => 0,
                                    ],
                            ],
                    ],
                    13 => [
                        'name' => 'EV_WEEK_NEXT',
                        'type' => 'EXPORT',
                        'optional' => 0,
                        'def' =>
                            [
                                0 =>
                                    [
                                        'name' => '',
                                        'abap' => 'N',
                                        'len' => 6,
                                        'dec' => 0,
                                        'offset' => 0,
                                    ],
                            ],
                    ],
                    14 => [
                        'name' => 'EV_YEAR',
                        'type' => 'EXPORT',
                        'optional' => 0,
                        'def' =>
                            [
                                0 =>
                                    [
                                        'name' => '',
                                        'abap' => 'N',
                                        'len' => 4,
                                        'dec' => 0,
                                        'offset' => 0,
                                    ],
                            ],
                    ],
                    15 => [
                        'name' => 'IV_DATE',
                        'type' => 'IMPORT',
                        'optional' => 1,
                        'def' =>
                            [
                                0 =>
                                    [
                                        'name' => '',
                                        'abap' => 'D',
                                        'len' => 8,
                                        'dec' => 0,
                                        'offset' => 0,
                                    ],
                            ],
                    ],
                ];
            }
            return false;
        });
        static::mock('saprfc_call_and_receive', function ($function) {
            if ($function === 'SAPRFC Z_MC_GET_DATE_TIME') {
                return 0;
            }
            return 1;
        });
        static::mock('saprfc_import', function ($function, $name, $param) {
            return ($function === 'SAPRFC Z_MC_GET_DATE_TIME'
                && $name === 'IV_DATE'
                && $param === '20181119'
            );
        });
        static::mock('saprfc_export', function ($function, $name) {
            if ($function !== 'SAPRFC Z_MC_GET_DATE_TIME') {
                return '';
            }
            switch ($name) {
                case 'EV_FRIDAY':
                    return '20181123';
                case 'EV_FRIDAY_LAST':
                    return '20181116';
                case 'EV_FRIDAY_NEXT':
                    return '20181130';
                case 'EV_FRITXT':
                    return 'Freitag';
                case 'EV_MONDAY':
                    return '20181119';
                case 'EV_MONDAY_LAST':
                    return '20181112';
                case 'EV_MONDAY_NEXT':
                    return '20181126';
                case 'EV_MONTH':
                    return '11';
                case 'EV_MONTH_LAST_DAY':
                    return '20181130';
                case 'EV_MONTXT':
                    return 'Montag';
                case 'EV_TIMESTAMP':
                    return '201811190000000';
                case 'EV_WEEK':
                    return '201847';
                case 'EV_WEEK_LAST':
                    return '201846';
                case 'EV_WEEK_NEXT':
                    return '201848';
                case 'EV_YEAR':
                    return '2018';
                default:
                    return '';
            }
        });
        static::mock('saprfc_function_free', function (&$function) {
            $function = null;
        });
    }

    /**
     * Mock the SAP RFC module for a failed SAP remote function call with parameters.
     */
    protected function mockFailedRemoteFunctionCallWithParameters()
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
            if ($connection === 'SAPRFC CONNECTION' && $name === 'Z_MC_GET_DATE_TIME') {
                return 'SAPRFC Z_MC_GET_DATE_TIME';
            }
            return false;
        });
        static::mock('saprfc_function_interface', function ($function) {
            if ($function === 'SAPRFC Z_MC_GET_DATE_TIME') {
                return [
                    0 => [
                        'name' => 'EV_FRIDAY',
                        'type' => 'EXPORT',
                        'optional' => 0,
                        'def' => [
                            0 => [
                                'name' => '',
                                'abap' => 'D',
                                'len' => 8,
                                'dec' => 0,
                                'offset' => 0,
                            ],
                        ],
                    ],
                    1 => [
                        'name' => 'EV_FRIDAY_LAST',
                        'type' => 'EXPORT',
                        'optional' => 0,
                        'def' => [
                            0 => [
                                'name' => '',
                                'abap' => 'D',
                                'len' => 8,
                                'dec' => 0,
                                'offset' => 0,
                            ],
                        ],
                    ],
                    2 => [
                        'name' => 'EV_FRIDAY_NEXT',
                        'type' => 'EXPORT',
                        'optional' => 0,
                        'def' => [
                            0 => [
                                'name' => '',
                                'abap' => 'D',
                                'len' => 8,
                                'dec' => 0,
                                'offset' => 0,
                            ],
                        ],
                    ],
                    3 => [
                        'name' => 'EV_FRITXT',
                        'type' => 'EXPORT',
                        'optional' => 0,
                        'def' => [
                            0 => [
                                'name' => '',
                                'abap' => 'C',
                                'len' => 15,
                                'dec' => 0,
                                'offset' => 0,
                            ],
                        ],
                    ],
                    4 => [
                        'name' => 'EV_MONDAY',
                        'type' => 'EXPORT',
                        'optional' => 0,
                        'def' =>
                            [
                                0 =>
                                    [
                                        'name' => '',
                                        'abap' => 'D',
                                        'len' => 8,
                                        'dec' => 0,
                                        'offset' => 0,
                                    ],
                            ],
                    ],
                    5 => [
                        'name' => 'EV_MONDAY_LAST',
                        'type' => 'EXPORT',
                        'optional' => 0,
                        'def' =>
                            [
                                0 =>
                                    [
                                        'name' => '',
                                        'abap' => 'D',
                                        'len' => 8,
                                        'dec' => 0,
                                        'offset' => 0,
                                    ],
                            ],
                    ],
                    6 => [
                        'name' => 'EV_MONDAY_NEXT',
                        'type' => 'EXPORT',
                        'optional' => 0,
                        'def' =>
                            [
                                0 =>
                                    [
                                        'name' => '',
                                        'abap' => 'D',
                                        'len' => 8,
                                        'dec' => 0,
                                        'offset' => 0,
                                    ],
                            ],
                    ],
                    7 => [
                        'name' => 'EV_MONTH',
                        'type' => 'EXPORT',
                        'optional' => 0,
                        'def' =>
                            [
                                0 =>
                                    [
                                        'name' => '',
                                        'abap' => 'N',
                                        'len' => 2,
                                        'dec' => 0,
                                        'offset' => 0,
                                    ],
                            ],
                    ],
                    8 => [
                        'name' => 'EV_MONTH_LAST_DAY',
                        'type' => 'EXPORT',
                        'optional' => 0,
                        'def' =>
                            [
                                0 =>
                                    [
                                        'name' => '',
                                        'abap' => 'D',
                                        'len' => 8,
                                        'dec' => 0,
                                        'offset' => 0,
                                    ],
                            ],
                    ],
                    9 => [
                        'name' => 'EV_MONTXT',
                        'type' => 'EXPORT',
                        'optional' => 0,
                        'def' =>
                            [
                                0 =>
                                    [
                                        'name' => '',
                                        'abap' => 'C',
                                        'len' => 15,
                                        'dec' => 0,
                                        'offset' => 0,
                                    ],
                            ],
                    ],
                    10 => [
                        'name' => 'EV_TIMESTAMP',
                        'type' => 'EXPORT',
                        'optional' => 0,
                        'def' =>
                            [
                                0 =>
                                    [
                                        'name' => '',
                                        'abap' => 'C',
                                        'len' => 14,
                                        'dec' => 0,
                                        'offset' => 0,
                                    ],
                            ],
                    ],
                    11 => [
                        'name' => 'EV_WEEK',
                        'type' => 'EXPORT',
                        'optional' => 0,
                        'def' =>
                            [
                                0 =>
                                    [
                                        'name' => '',
                                        'abap' => 'N',
                                        'len' => 6,
                                        'dec' => 0,
                                        'offset' => 0,
                                    ],
                            ],
                    ],
                    12 => [
                        'name' => 'EV_WEEK_LAST',
                        'type' => 'EXPORT',
                        'optional' => 0,
                        'def' =>
                            [
                                0 =>
                                    [
                                        'name' => '',
                                        'abap' => 'N',
                                        'len' => 6,
                                        'dec' => 0,
                                        'offset' => 0,
                                    ],
                            ],
                    ],
                    13 => [
                        'name' => 'EV_WEEK_NEXT',
                        'type' => 'EXPORT',
                        'optional' => 0,
                        'def' =>
                            [
                                0 =>
                                    [
                                        'name' => '',
                                        'abap' => 'N',
                                        'len' => 6,
                                        'dec' => 0,
                                        'offset' => 0,
                                    ],
                            ],
                    ],
                    14 => [
                        'name' => 'EV_YEAR',
                        'type' => 'EXPORT',
                        'optional' => 0,
                        'def' =>
                            [
                                0 =>
                                    [
                                        'name' => '',
                                        'abap' => 'N',
                                        'len' => 4,
                                        'dec' => 0,
                                        'offset' => 0,
                                    ],
                            ],
                    ],
                    15 => [
                        'name' => 'IV_DATE',
                        'type' => 'IMPORT',
                        'optional' => 1,
                        'def' =>
                            [
                                0 =>
                                    [
                                        'name' => '',
                                        'abap' => 'D',
                                        'len' => 8,
                                        'dec' => 0,
                                        'offset' => 0,
                                    ],
                            ],
                    ],
                ];
            }
            return false;
        });
        static::mock('saprfc_call_and_receive', function ($function) {
            if ($function === 'SAPRFC Z_MC_GET_DATE_TIME') {
                return 1;
            }
            throw new \RuntimeException('Unexpected function instance.');
        });
        static::mock('saprfc_import', function ($function, $name, $param) {
            return ($function === 'SAPRFC Z_MC_GET_DATE_TIME'
                && $name === 'IV_DATE'
                && $param === '2018-11-19'
            );
        });
        static::mock('saprfc_function_free', function (&$function) {
            $function = null;
        });
    }
}
