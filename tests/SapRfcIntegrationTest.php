<?php

namespace tests\phpsap\saprfc;

use phpsap\IntegrationTests\AbstractConnectionTestCase;
use phpsap\IntegrationTests\AbstractSapRfcTestCase;
use RuntimeException;

/**
 * Class tests\phpsap\saprfc\SapRfcIntegrationTest
 *
 * Implement methods of the integration tests to mock SAP remote function
 * calls without an actual SAP system for testing.
 *
 * @package tests\phpsap\saprfc
 * @author Gregor J.
 * @license MIT
 */
class SapRfcIntegrationTest extends AbstractSapRfcTestCase
{
    use TestCaseTrait;

    /**
     * @var array The raw API of RFC_WALK_THRU_TEST as seen by the module.
     */
    public static $rfcWalkThruTestApi = [
        0 =>
            [
                'name' => 'TEST_OUT',
                'type' => 'EXPORT',
                'optional' => 0,
                'def' =>
                    [
                        0 =>
                            [
                                'name' => 'RFCFLOAT',
                                'abap' => 'F',
                                'len' => 8,
                                'dec' => 0,
                                'offset' => 0,
                            ],
                        1 =>
                            [
                                'name' => 'RFCCHAR1',
                                'abap' => 'C',
                                'len' => 1,
                                'dec' => 0,
                                'offset' => 8,
                            ],
                        2 =>
                            [
                                'name' => 'RFCINT2',
                                'abap' => 's',
                                'len' => 2,
                                'dec' => 0,
                                'offset' => 10,
                            ],
                        3 =>
                            [
                                'name' => 'RFCINT1',
                                'abap' => 'b',
                                'len' => 1,
                                'dec' => 0,
                                'offset' => 12,
                            ],
                        4 =>
                            [
                                'name' => 'RFCCHAR4',
                                'abap' => 'C',
                                'len' => 4,
                                'dec' => 0,
                                'offset' => 13,
                            ],
                        5 =>
                            [
                                'name' => 'RFCINT4',
                                'abap' => 'I',
                                'len' => 4,
                                'dec' => 0,
                                'offset' => 20,
                            ],
                        6 =>
                            [
                                'name' => 'RFCHEX3',
                                'abap' => 'X',
                                'len' => 3,
                                'dec' => 0,
                                'offset' => 24,
                            ],
                        7 =>
                            [
                                'name' => 'RFCCHAR2',
                                'abap' => 'C',
                                'len' => 2,
                                'dec' => 0,
                                'offset' => 27,
                            ],
                        8 =>
                            [
                                'name' => 'RFCTIME',
                                'abap' => 'T',
                                'len' => 6,
                                'dec' => 0,
                                'offset' => 29,
                            ],
                        9 =>
                            [
                                'name' => 'RFCDATE',
                                'abap' => 'D',
                                'len' => 8,
                                'dec' => 0,
                                'offset' => 35,
                            ],
                        10 =>
                            [
                                'name' => 'RFCDATA1',
                                'abap' => 'C',
                                'len' => 50,
                                'dec' => 0,
                                'offset' => 43,
                            ],
                        11 =>
                            [
                                'name' => 'RFCDATA2',
                                'abap' => 'C',
                                'len' => 50,
                                'dec' => 0,
                                'offset' => 93,
                            ],
                    ],
            ],
        1 =>
            [
                'name' => 'TEST_IN',
                'type' => 'IMPORT',
                'optional' => 0,
                'def' =>
                    [
                        0 =>
                            [
                                'name' => 'RFCFLOAT',
                                'abap' => 'F',
                                'len' => 8,
                                'dec' => 0,
                                'offset' => 0,
                            ],
                        1 =>
                            [
                                'name' => 'RFCCHAR1',
                                'abap' => 'C',
                                'len' => 1,
                                'dec' => 0,
                                'offset' => 8,
                            ],
                        2 =>
                            [
                                'name' => 'RFCINT2',
                                'abap' => 's',
                                'len' => 2,
                                'dec' => 0,
                                'offset' => 10,
                            ],
                        3 =>
                            [
                                'name' => 'RFCINT1',
                                'abap' => 'b',
                                'len' => 1,
                                'dec' => 0,
                                'offset' => 12,
                            ],
                        4 =>
                            [
                                'name' => 'RFCCHAR4',
                                'abap' => 'C',
                                'len' => 4,
                                'dec' => 0,
                                'offset' => 13,
                            ],
                        5 =>
                            [
                                'name' => 'RFCINT4',
                                'abap' => 'I',
                                'len' => 4,
                                'dec' => 0,
                                'offset' => 20,
                            ],
                        6 =>
                            [
                                'name' => 'RFCHEX3',
                                'abap' => 'X',
                                'len' => 3,
                                'dec' => 0,
                                'offset' => 24,
                            ],
                        7 =>
                            [
                                'name' => 'RFCCHAR2',
                                'abap' => 'C',
                                'len' => 2,
                                'dec' => 0,
                                'offset' => 27,
                            ],
                        8 =>
                            [
                                'name' => 'RFCTIME',
                                'abap' => 'T',
                                'len' => 6,
                                'dec' => 0,
                                'offset' => 29,
                            ],
                        9 =>
                            [
                                'name' => 'RFCDATE',
                                'abap' => 'D',
                                'len' => 8,
                                'dec' => 0,
                                'offset' => 35,
                            ],
                        10 =>
                            [
                                'name' => 'RFCDATA1',
                                'abap' => 'C',
                                'len' => 50,
                                'dec' => 0,
                                'offset' => 43,
                            ],
                        11 =>
                            [
                                'name' => 'RFCDATA2',
                                'abap' => 'C',
                                'len' => 50,
                                'dec' => 0,
                                'offset' => 93,
                            ],
                    ],
            ],
        2 =>
            [
                'name' => 'DESTINATIONS',
                'type' => 'TABLE',
                'optional' => 0,
                'def' =>
                    [
                        0 =>
                            [
                                'name' => 'RFCDEST',
                                'abap' => 'C',
                                'len' => 32,
                                'dec' => 0,
                                'offset' => 0,
                            ],
                    ],
            ],
        3 =>
            [
                'name' => 'LOG',
                'type' => 'TABLE',
                'optional' => 0,
                'def' =>
                    [
                        0 =>
                            [
                                'name' => 'RFCDEST',
                                'abap' => 'C',
                                'len' => 32,
                                'dec' => 0,
                                'offset' => 0,
                            ],
                        1 =>
                            [
                                'name' => 'RFCWHOAMI',
                                'abap' => 'C',
                                'len' => 32,
                                'dec' => 0,
                                'offset' => 32,
                            ],
                        2 =>
                            [
                                'name' => 'RFCLOG',
                                'abap' => 'C',
                                'len' => 70,
                                'dec' => 0,
                                'offset' => 64,
                            ],
                    ],
            ],
    ];

    /**
     * @var array The raw API of RFC_READ_TABLE as seen by the module.
     */
    public static $rfcReadTableApi = [
        0 =>
            [
                'name' => 'DELIMITER',
                'type' => 'IMPORT',
                'optional' => 1,
                'def' =>
                    [
                        0 =>
                            [
                                'name' => '',
                                'abap' => 'C',
                                'len' => 1,
                                'dec' => 0,
                                'offset' => 0,
                            ],
                    ],
            ],
        1 =>
            [
                'name' => 'NO_DATA',
                'type' => 'IMPORT',
                'optional' => 1,
                'def' =>
                    [
                        0 =>
                            [
                                'name' => '',
                                'abap' => 'C',
                                'len' => 1,
                                'dec' => 0,
                                'offset' => 0,
                            ],
                    ],
            ],
        2 =>
            [
                'name' => 'QUERY_TABLE',
                'type' => 'IMPORT',
                'optional' => 0,
                'def' =>
                    [
                        0 =>
                            [
                                'name' => '',
                                'abap' => 'C',
                                'len' => 30,
                                'dec' => 0,
                                'offset' => 0,
                            ],
                    ],
            ],
        3 =>
            [
                'name' => 'ROWCOUNT',
                'type' => 'IMPORT',
                'optional' => 1,
                'def' =>
                    [
                        0 =>
                            [
                                'name' => '',
                                'abap' => 'I',
                                'len' => 4,
                                'dec' => 0,
                                'offset' => 0,
                            ],
                    ],
            ],
        4 =>
            [
                'name' => 'ROWSKIPS',
                'type' => 'IMPORT',
                'optional' => 1,
                'def' =>
                    [
                        0 =>
                            [
                                'name' => '',
                                'abap' => 'I',
                                'len' => 4,
                                'dec' => 0,
                                'offset' => 0,
                            ],
                    ],
            ],
        5 =>
            [
                'name' => 'DATA',
                'type' => 'TABLE',
                'optional' => 0,
                'def' =>
                    [
                        0 =>
                            [
                                'name' => 'WA',
                                'abap' => 'C',
                                'len' => 512,
                                'dec' => 0,
                                'offset' => 0,
                            ],
                    ],
            ],
        6 =>
            [
                'name' => 'FIELDS',
                'type' => 'TABLE',
                'optional' => 0,
                'def' =>
                    [
                        0 =>
                            [
                                'name' => 'FIELDNAME',
                                'abap' => 'C',
                                'len' => 30,
                                'dec' => 0,
                                'offset' => 0,
                            ],
                        1 =>
                            [
                                'name' => 'OFFSET',
                                'abap' => 'N',
                                'len' => 6,
                                'dec' => 0,
                                'offset' => 30,
                            ],
                        2 =>
                            [
                                'name' => 'LENGTH',
                                'abap' => 'N',
                                'len' => 6,
                                'dec' => 0,
                                'offset' => 36,
                            ],
                        3 =>
                            [
                                'name' => 'TYPE',
                                'abap' => 'C',
                                'len' => 1,
                                'dec' => 0,
                                'offset' => 42,
                            ],
                        4 =>
                            [
                                'name' => 'FIELDTEXT',
                                'abap' => 'C',
                                'len' => 60,
                                'dec' => 0,
                                'offset' => 43,
                            ],
                    ],
            ],
        7 =>
            [
                'name' => 'OPTIONS',
                'type' => 'TABLE',
                'optional' => 0,
                'def' =>
                    [
                        0 =>
                            [
                                'name' => 'TEXT',
                                'abap' => 'C',
                                'len' => 72,
                                'dec' => 0,
                                'offset' => 0,
                            ],
                    ],
            ],
    ];

    /**
     * @inheritDoc
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

    /**
     * @inheritDoc
     */
    protected function mockSuccessfulRfcPing()
    {
        static::mock('saprfc_open', static function ($config) {
            if (is_array($config)) {
                return 'SAPRFC CONNECTION';
            }
            return false;
        });
        static::mock('saprfc_close', static function (&$connection) {
            $connection = null;
        });
        static::mock('saprfc_function_discover', static function ($connection, $name) {
            if ($connection === 'SAPRFC CONNECTION' && $name === 'RFC_PING') {
                return 'SAPRFC PING';
            }
            return false;
        });
        static::mock('saprfc_call_and_receive', static function ($function) {
            if ($function === 'SAPRFC PING') {
                return 0;
            }
            return 1;
        });
        static::mock('saprfc_function_free', static function (&$function) {
            $function = null;
        });
        static::mock('saprfc_function_interface', static function ($function) {
            if ($function === 'SAPRFC PING') {
                return [];
            }
            return false;
        });
    }

    /**
     * @inheritDoc
     */
    protected function mockUnknownFunctionException()
    {
        static::mock('saprfc_open', static function ($config) {
            if (is_array($config)) {
                return 'SAPRFC CONNECTION';
            }
            return false;
        });
        static::mock('saprfc_close', static function (&$connection) {
            $connection = null;
        });
        static::mock('saprfc_function_discover', static function ($connection, $name) {
            if ($connection === 'SAPRFC CONNECTION' && $name === 'RFC_PINGG') {
                return false;
            }
            return $name;
        });
        static::mock('saprfc_error', static function () {
            return 'function RFC_PINGG not found';
        });
        static::mock('saprfc_function_free', static function (&$function) {
            $function = null;
        });
    }

    /**
     * @inheritDoc
     */
    protected function mockRemoteFunctionCallWithParametersAndResults()
    {
        static::mock('saprfc_open', static function ($config) {
            if (is_array($config)) {
                return 'SAPRFC CONNECTION';
            }
            return false;
        });
        static::mock('saprfc_close', static function (&$connection) {
            $connection = null;
        });
        static::mock('saprfc_function_discover', static function ($connection, $name) {
            if ($connection === 'SAPRFC CONNECTION' && $name === 'RFC_WALK_THRU_TEST') {
                return 'SAPRFC RFC_WALK_THRU_TEST';
            }
            return false;
        });
        static::mock('saprfc_function_interface', static function ($function) {
            if ($function === 'SAPRFC RFC_WALK_THRU_TEST') {
                return static::$rfcWalkThruTestApi;
            }
            return false;
        });
        static::mock('saprfc_call_and_receive', static function ($function) {
            if ($function === 'SAPRFC RFC_WALK_THRU_TEST') {
                return 0;
            }
            return 1;
        });
        static::mock('saprfc_import', static function ($function, $name, $param) {
            return ($function === 'SAPRFC RFC_WALK_THRU_TEST'
                && $name === 'TEST_IN'
                && is_array($param)
            );
        });
        static::mock('saprfc_table_init', static function ($function, $name) {
            return ($function === 'SAPRFC RFC_WALK_THRU_TEST'
                && in_array($name, ['LOG', 'DESTINATIONS'])
            );
        });
        static::mock('saprfc_table_append', static function ($function, $name, $param) {
            return ($function === 'SAPRFC RFC_WALK_THRU_TEST'
                && $name === 'DESTINATIONS'
                && is_array($param)
            );
        });
        static::mock('saprfc_table_append', static function ($function, $name, $param) {
            return ($function === 'SAPRFC RFC_WALK_THRU_TEST'
                && $name === 'DESTINATIONS'
                && is_array($param)
                && $param === ['RFCDEST' => 'AOP3']
            );
        });
        static::mock('saprfc_table_rows', static function ($function, $name) {
            if ($function !== 'SAPRFC RFC_WALK_THRU_TEST') {
                return false;
            }
            switch ($name) {
                case 'LOG':
                    return 1;
                case 'DESTINATIONS':
                    return 0;
                default:
                    return false;
            }
        });
        static::mock('saprfc_table_read', static function ($function, $name, $param) {
            if ($function === 'SAPRFC RFC_WALK_THRU_TEST' && $name === 'LOG' && $param === 1) {
                return [
                    'RFCDEST' => 'AOP3',
                    'RFCWHOAMI' => 'pzjti000',
                    'RFCLOG' => 'FAP-RytEHBsRYKX AOP3 eumqvMJD ZLqovj.' //just some random characters around AOP3
                ];
            }
            return false;
        });
        static::mock('saprfc_export', static function ($function, $name) {
            if ($function === 'SAPRFC RFC_WALK_THRU_TEST' && $name === 'TEST_OUT') {
                return [
                    'RFCFLOAT' => 70.11,
                    'RFCCHAR1' => 'A',
                    'RFCINT2' => 4095,
                    'RFCINT1' => 163,
                    'RFCCHAR4' => 'QqMh',
                    'RFCINT4' => 416639,
                    'RFCHEX3' => '53', //=S
                    'RFCCHAR2' => 'XC',
                    'RFCTIME' => '102030',
                    'RFCDATE' => '20191030',
                    'RFCDATA1' => 'qKWjmNfad32rfS9Z',
                    'RFCDATA2' => 'xi82ph2zJ8BCVtlR'
                ];
            }
            return false;
        });
        static::mock('saprfc_function_free', static function (&$function) {
            $function = null;
        });
    }

    /**
     * @inheritDoc
     */
    protected function mockFailedRemoteFunctionCallWithParameters()
    {
        static::mock('saprfc_open', static function ($config) {
            if (is_array($config)) {
                return 'SAPRFC CONNECTION';
            }
            return false;
        });
        static::mock('saprfc_close', static function (&$connection) {
            $connection = null;
        });
        static::mock('saprfc_function_discover', static function ($connection, $name) {
            if ($connection === 'SAPRFC CONNECTION' && $name === 'RFC_READ_TABLE') {
                return 'SAPRFC RFC_READ_TABLE';
            }
            return false;
        });
        static::mock('saprfc_function_interface', static function ($function) {
            if ($function === 'SAPRFC RFC_READ_TABLE') {
                return static::$rfcReadTableApi;
            }
            return false;
        });
        static::mock('saprfc_call_and_receive', static function ($function) {
            if ($function === 'SAPRFC RFC_READ_TABLE') {
                return 1;
            }
            throw new RuntimeException('Unexpected function instance.');
        });
        static::mock('saprfc_import', static function ($function, $name, $param) {
            return ($function === 'SAPRFC RFC_READ_TABLE'
                && $name === 'QUERY_TABLE'
                && $param === '&'
            );
        });
        static::mock('saprfc_table_init', static function ($function, $name) {
            return ($function === 'SAPRFC RFC_READ_TABLE'
                && in_array($name, ['DATA', 'FIELDS', 'OPTIONS'])
            );
        });
        static::mock('saprfc_exception', static function ($function) {
            if ($function === 'SAPRFC RFC_READ_TABLE') {
                return 'My exception message.';
            }
            throw new RuntimeException('Expected RFC_READ_TABLE function!');
        });
        static::mock('saprfc_function_free', static function (&$function) {
            $function = null;
        });
    }
}
