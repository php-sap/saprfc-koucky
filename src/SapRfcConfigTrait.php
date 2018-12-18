<?php
/**
 * File src/SapRfcConfigTrait.php
 *
 * Common code for connection configuration.
 *
 * @package saprfc-koucky
 * @author  Gregor J.
 * @license MIT
 */
namespace phpsap\saprfc;

/**
 * Trait SapRfcConfigTrait
 *
 * Common code for connection configuration.
 *
 * @package phpsap\saprfc
 * @author  Gregor J.
 * @license MIT
 */
trait SapRfcConfigTrait
{
    /**
     * @var array list all connection parameters available
     */
    protected static $conParamAvail = [
        'ASHOST',
        'SYSNR',
        'CLIENT',
        'USER',
        'PASSWD',
        'GWHOST',
        'GWSERV',
        'MSHOST',
        'R3NAME',
        'GROUP',
        'LANG',
        'TRACE'
    ];

    /**
     * Generate the configuration array needed for connecting a remote SAP system
     * using Eduard Kouckys saprfc module.
     * @return array
     */
    public function generateConfig()
    {
        $config = [];
        foreach ($this->config as $key => $value) {
            $key = strtoupper($key);
            if (in_array($key, static::$conParamAvail, true)) {
                $config[$key] = $value;
            }
        }
        return $config;
    }
}
