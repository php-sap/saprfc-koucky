<?php
/**
 * File tests/SapRfcConfigBTest.php
 *
 * Test config type B.
 *
 * @package saprfc-koucky
 * @author  Gregor J.
 * @license MIT
 */

namespace tests\phpsap\saprfc;

use phpsap\classes\AbstractConfigB;
use phpsap\exceptions\IncompleteConfigException;
use phpsap\interfaces\IConfig;
use phpsap\interfaces\IConfigB;
use phpsap\saprfc\SapRfcConfigB;

/**
 * Class tests\phpsap\saprfc\SapRfcConfigBTest
 *
 * Test config type B.
 *
 * @package tests\phpsap\saprfc
 * @author  Gregor J.
 * @license MIT
 */
class SapRfcConfigBTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test config type B inheritance chain.
     */
    public function testInheritance()
    {
        $config = new SapRfcConfigB();
        static::assertInstanceOf(IConfig::class, $config);
        static::assertInstanceOf(IConfigB::class, $config);
        static::assertInstanceOf(AbstractConfigB::class, $config);
        static::assertInstanceOf(SapRfcConfigB::class, $config);
    }

    /**
     * Test a valid config creation.
     */
    public function testValidConfig()
    {
        $configArr = [
            'client' => '02',
            'user' => 'username',
            'passwd' => 'password',
            'mshost' => 'sap.example.com',
            'r3name' => 'system_id',
            'group' => 'logon_group',
            'lang' => 'EN',
            'trace' => SapRfcConfigB::TRACE_VERBOSE
        ];
        $configJson = json_encode($configArr);
        $config = new SapRfcConfigB($configJson);
        $configSaprfc = $config->generateConfig();
        static::assertInternalType('array', $configSaprfc);
        static::assertArrayHasKey('CLIENT', $configSaprfc);
        static::assertSame('02', $configSaprfc['CLIENT']);
        static::assertArrayHasKey('USER', $configSaprfc);
        static::assertSame('username', $configSaprfc['USER']);
        static::assertArrayHasKey('PASSWD', $configSaprfc);
        static::assertSame('password', $configSaprfc['PASSWD']);
        static::assertArrayHasKey('MSHOST', $configSaprfc);
        static::assertSame('sap.example.com', $configSaprfc['MSHOST']);
        static::assertArrayHasKey('R3NAME', $configSaprfc);
        static::assertSame('system_id', $configSaprfc['R3NAME']);
        static::assertArrayHasKey('GROUP', $configSaprfc);
        static::assertSame('logon_group', $configSaprfc['GROUP']);
        static::assertArrayHasKey('LANG', $configSaprfc);
        static::assertSame('EN', $configSaprfc['LANG']);
        static::assertArrayHasKey('TRACE', $configSaprfc);
        static::assertSame(SapRfcConfigB::TRACE_VERBOSE, $configSaprfc['TRACE']);
    }

    /**
     * Data provider for incomplete config.
     * @return array
     */
    public static function incompleteConfig()
    {
        return [
            [
                [
                    'client' => '02',
                    'user' => 'username',
                    'passwd' => 'password',
                    'r3name' => 'system_id',
                    'group' => 'logon_group'
                ],
                'mshost'
            ],
            [
                [
                    'client' => '02',
                    'user' => 'username',
                    'passwd' => 'password',
                    'mshost' => 'sap.example.com',
                    'group' => 'logon_group',
                    'lang' => 'EN',
                    'trace' => SapRfcConfigB::TRACE_OFF
                ],
                'r3name'
            ]
        ];
    }

    /**
     * Test incomplete config exception.
     * @param array $configArr
     * @param string $missing
     * @dataProvider incompleteConfig
     */
    public function testIncompleteConfig($configArr, $missing)
    {
        $configJson = json_encode($configArr);
        $config = new SapRfcConfigB($configJson);
        $expectedMsg = sprintf('Missing mandatory key %s.', $missing);
        $this->setExpectedException(IncompleteConfigException::class, $expectedMsg);
        $config->generateConfig();
    }
}
