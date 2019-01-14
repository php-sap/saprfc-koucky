<?php
/**
 * File tests/SapRfcFunctionTest.php
 *
 * Test function class.
 *
 * @package saprfc-koucky
 * @author  Gregor J.
 * @license MIT
 */

namespace tests\phpsap\saprfc;

use phpsap\saprfc\SapRfcConnection;
use tests\phpsap\saprfc\helper\AbstractSaprfcTests;

/**
 * Class tests\phpsap\saprfc\SapRfcFunctionTest
 *
 * Test function class.
 *
 * @package tests\phpsap\saprfc
 * @author  Gregor J.
 * @license MIT
 */
class SapRfcFunctionTest extends AbstractSaprfcTests
{
    /**
     * Mock SAPRFC functions necessary to perform a successful SAP remote function
     * call.
     */
    private static function mockSaprfcSuccessfulFunctionCall()
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
            return false;
        });
    }

    /**
     * Test successfully invoking a SAP remote function call.
     */
    public function testSuccessfulFunctionCall()
    {
        if (!extension_loaded('saprfc')) {
            //load functions mocking saprfc module functions
            static::mockSaprfcSuccessfulFunctionCall();
            //load a bogus config
            $config = static::getOfflineSapConfig();
        } else {
            //load a valid config
            $config = static::getOnlineSapConfig('sap');
        }
        $connection = new SapRfcConnection($config);
        $function = $connection->prepareFunction('RFC_PING');
        $result = $function->invoke();
        static::assertSame([], $result);
    }

    /**
     * Mock SAPRFC functions necessary to perform a successful SAP remote function
     * call.
     */
    private static function mockUnknownFunctionException()
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
            if ($connection === 'SAPRFC CONNECTION' && $name === 'RFC_PINGG') {
                return false;
            }
            return $name;
        });
        static::mockSaprfcFunction('saprfc_error', function () {
            return 'function RFC_PINGG not found';
        });
        static::mockSaprfcFunction('saprfc_function_free', function (&$function) {
            $function = null;
        });
    }

    /**
     * Test invoking an unknown function an receiving an exception.
     * @expectedException \phpsap\exceptions\UnknownFunctionException
     * @expectedExceptionMessageRegExp "^Unknown function RFC_PINGG: .*"
     */
    public function testUnknownFunctionException()
    {
        if (!extension_loaded('saprfc')) {
            //load functions mocking saprfc module functions
            static::mockUnknownFunctionException();
            //load a bogus config
            $config = static::getOfflineSapConfig();
        } else {
            //load a valid config
            $config = static::getOnlineSapConfig('sap');
        }
        $connection = new SapRfcConnection($config);
        $function = $connection->prepareFunction('RFC_PINGG');
        $function->invoke();
    }

    /**
     * Mock SAPRFC functions necessary to perform a successful SAP remote function
     * call.
     */
    private static function mockRemoteFunctionCallWithParametersAndResults()
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
            if ($connection === 'SAPRFC CONNECTION' && $name === 'Z_MC_GET_DATE_TIME') {
                return 'SAPRFC Z_MC_GET_DATE_TIME';
            }
            return false;
        });
        static::mockSaprfcFunction('saprfc_function_interface', function ($function) {
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
        static::mockSaprfcFunction('saprfc_call_and_receive', function ($function) {
            if ($function === 'SAPRFC Z_MC_GET_DATE_TIME') {
                return 0;
            }
            return 1;
        });
        static::mockSaprfcFunction('saprfc_import', function ($function, $name, $param) {
            return ($function === 'SAPRFC Z_MC_GET_DATE_TIME'
                && $name === 'IV_DATE'
                && $param === '20181119'
            );
        });
        static::mockSaprfcFunction('saprfc_export', function ($function, $name) {
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
        static::mockSaprfcFunction('saprfc_function_free', function (&$function) {
            $function = null;
        });
    }

    /**
     * Test successful SAP remote function call with parameters and results.
     */
    public function testRemoteFunctionCallWithParametersAndResults()
    {
        if (!extension_loaded('saprfc')) {
            //load functions mocking saprfc module functions
            static::mockRemoteFunctionCallWithParametersAndResults();
            //load a bogus config
            $config = static::getOfflineSapConfig();
        } else {
            //load a valid config
            $config = static::getOnlineSapConfig('sap');
        }
        $connection = new SapRfcConnection($config);
        $function = $connection->prepareFunction('Z_MC_GET_DATE_TIME');
        $function->setParam('IV_DATE', '20181119');
        $result = $function->invoke();
        $expected = [
            'EV_FRIDAY' => '20181123',
            'EV_FRIDAY_LAST' => '20181116',
            'EV_FRIDAY_NEXT' => '20181130',
            'EV_FRITXT' => 'Freitag',
            'EV_MONDAY' => '20181119',
            'EV_MONDAY_LAST' => '20181112',
            'EV_MONDAY_NEXT' => '20181126',
            'EV_MONTH' => '11',
            'EV_MONTH_LAST_DAY' => '20181130',
            'EV_MONTXT' => 'Montag',
            'EV_TIMESTAMP' => 'NOVALUE',
            'EV_WEEK' => '201847',
            'EV_WEEK_LAST' => '201846',
            'EV_WEEK_NEXT' => '201848',
            'EV_YEAR' => '2018'
        ];
        static::assertInternalType('array', $result);
        foreach ($expected as $name => $value) {
            static::assertArrayHasKey($name, $result);
            if ($name === 'EV_TIMESTAMP') {
                continue;
            }
            static::assertSame($value, $result[$name]);
        }
    }
}
