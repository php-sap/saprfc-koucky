<?php
/**
 * File src/AbstractRemoteFunctionCall.php
 *
 * PHP/SAP proxy class for SAP remote function calls.
 *
 * @package saprfc-harding
 * @author  Gregor J.
 * @license MIT
 */

namespace phpsap\saprfc;

use phpsap\interfaces\exceptions\IIncompleteConfigException;
use phpsap\interfaces\IConfig;
use phpsap\interfaces\IConnection;

/**
 * Class phpsap\saprfc\AbstractRemoteFunctionCall
 *
 * Abstract class handling a PHP/SAP connection and remote function.
 *
 * @package phpsap\saprfc
 * @author  Gregor J.
 * @license MIT
 */
abstract class AbstractRemoteFunctionCall extends \phpsap\classes\AbstractRemoteFunctionCall
{
    /**
     * Create a connection instance using the given config.
     * @param IConfig $config
     * @return IConnection|SapRfcConnection
     * @throws IIncompleteConfigException
     */
    protected function createConnectionInstance(IConfig $config)
    {
        return new SapRfcConnection($config);
    }
}
