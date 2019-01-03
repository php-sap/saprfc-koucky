<?php
/**
 * File tests/SapRfcConfigATest.php
 *
 * Test config type A.
 *
 * @package saprfc-koucky
 * @author  Gregor J.
 * @license MIT
 */

namespace tests\phpsap\saprfc;

use phpsap\classes\AbstractConfigA;
use phpsap\exceptions\IncompleteConfigException;
use phpsap\interfaces\IConfig;
use phpsap\interfaces\IConfigA;
use phpsap\saprfc\SapRfcConfigA;

/**
 * Class tests\phpsap\saprfc\SapRfcConfigATest
 *
 * Test config type A.
 *
 * @package tests\phpsap\saprfc
 * @author  Gregor J.
 * @license MIT
 */
class SapRfcConfigATest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test config type A inheritance chain.
     */
    public function testInheritance()
    {
        $config = new SapRfcConfigA();
        static::assertInstanceOf(IConfig::class, $config);
        static::assertInstanceOf(IConfigA::class, $config);
        static::assertInstanceOf(AbstractConfigA::class, $config);
        static::assertInstanceOf(SapRfcConfigA::class, $config);
    }

    /**
     * Test a valid config creation.
     */
    public function testValidConfig()
    {
        $configArr = [
            'ashost' => 'sap.example.com',
            'sysnr' => '000',
            'client' => '01',
            'user' => 'username',
            'passwd' => 'password',
            'gwhost' => 'gw.example.com',
            'gwserv' => 'abc',
            'lang' => 'EN',
            'trace' => 3
        ];
        $configJson = json_encode($configArr);
        $config = new SapRfcConfigA($configJson);
        $configSaprfc = $config->generateConfig();
        static::assertInternalType('array', $configSaprfc);
        static::assertArrayHasKey('ASHOST', $configSaprfc);
        static::assertSame('sap.example.com', $configSaprfc['ASHOST']);
        static::assertArrayHasKey('SYSNR', $configSaprfc);
        static::assertSame('000', $configSaprfc['SYSNR']);
        static::assertArrayHasKey('CLIENT', $configSaprfc);
        static::assertSame('01', $configSaprfc['CLIENT']);
        static::assertArrayHasKey('USER', $configSaprfc);
        static::assertSame('username', $configSaprfc['USER']);
        static::assertArrayHasKey('PASSWD', $configSaprfc);
        static::assertSame('password', $configSaprfc['PASSWD']);
        static::assertArrayHasKey('GWHOST', $configSaprfc);
        static::assertSame('gw.example.com', $configSaprfc['GWHOST']);
        static::assertArrayHasKey('GWSERV', $configSaprfc);
        static::assertSame('abc', $configSaprfc['GWSERV']);
        static::assertArrayHasKey('LANG', $configSaprfc);
        static::assertSame('EN', $configSaprfc['LANG']);
        static::assertArrayHasKey('TRACE', $configSaprfc);
        static::assertSame(3, $configSaprfc['TRACE']);
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
                    'ashost' => 'sap.example.com',
                    'sysnr' => '000',
                    'client' => '01',
                    'user' => 'username'
                ],
                'passwd'
            ],
            [
                [
                    'ashost' => 'sap.example.com',
                    'sysnr' => '000',
                    'user' => 'username',
                    'passwd' => 'password',
                    'gwhost' => 'gw.example.com',
                    'gwserv' => 'abc',
                    'lang' => 'EN',
                    'trace' => 3
                ],
                'client'
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
        $config = new SapRfcConfigA($configJson);
        $expectedMsg = sprintf('Missing mandatory key %s.', $missing);
        $this->setExpectedException(IncompleteConfigException::class, $expectedMsg);
        $config->generateConfig();
    }
}
