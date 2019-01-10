<?php
/**
 * File src/SapRfcConnection.phpon.php
 *
 * PHP/SAP connections using Eduard Kouckys saprfc module.
 *
 * @package saprfc-koucky
 * @author  Gregor J.
 * @license MIT
 */

namespace phpsap\saprfc;

use phpsap\classes\AbstractConnection;
use phpsap\exceptions\ConnectionFailedException;
use phpsap\exceptions\FunctionCallException;

/**
 * Class phpsap\saprfc\SapRfcConnection
 *
 * PHP/SAP connection class abstracting connection related functions using Eduard
 * Kouckys saprfc module.
 *
 * @package phpsap\saprfc
 * @author  Gregor J.
 * @license MIT
 */
class SapRfcConnection extends AbstractConnection
{
    /**
     * Send a ping request via an established connection to verify that the
     * connection works.
     * @return boolean success?
     * @throws \phpsap\exceptions\ConnectionFailedException
     */
    public function ping()
    {
        $ping = $this->createFunctionInstance('RFC_PING');
        try {
            $ping->invoke();
        } catch (FunctionCallException $fcex) {
            return false;
        }
        return true;
    }

    /**
     * Closes the connection instance of the underlying PHP module.
     */
    public function close()
    {
        if ($this->isConnected()) {
            @saprfc_close($this->connection);
            $this->connection = null;
        }
    }

    /**
     * Prepare a remote function call and return a function instance.
     * @param string $name
     * @return \phpsap\saprfc\SapRfcFunction
     * @throws \phpsap\exceptions\ConnectionFailedException
     */
    protected function createFunctionInstance($name)
    {
        return new SapRfcFunction($this->getConnection(), $name);
    }

    /**
     * Creates a connection using the underlying PHP module.
     * @throws \phpsap\exceptions\ConnectionFailedException
     */
    public function connect()
    {
        if ($this->isConnected()) {
            $this->close();
        }
        $this->connection = @saprfc_open($this->config);
        if ($this->connection === false) {
            $this->connection = null;
            throw new ConnectionFailedException(sprintf(
                'Connection %s creation failed: %s',
                $this->getId(),
                @saprfc_error()
            ));
        }
    }
}
