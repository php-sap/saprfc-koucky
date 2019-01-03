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
     * @var int PHP module return value reporting everything was O.K.
     */
    const SAPRFC_OK = 0;

    /**
     * Send a ping request via an established connection to verify that the
     * connection works.
     * @return boolean success?
     * @throws \phpsap\exceptions\ConnectionFailedException
     */
    public function ping()
    {
        $this->connect();
        $ping = @saprfc_function_discover($this->connection, 'RFC_PING');
        if ($ping === false) {
            /** @noinspection ForgottenDebugOutputInspection */
            error_log(sprintf(
                'saprfc function discover RFC_PING failed: %s',
                @saprfc_error()
            ));
            $this->close();
            return false;
        }
        $result = @saprfc_call_and_receive($ping);
        @saprfc_function_free($ping);
        return ($result === static::SAPRFC_OK);
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
    public function prepareFunction($name)
    {
        if (!is_string($name) || empty($name)) {
            throw new \InvalidArgumentException(
                'Missing or malformed SAP remote function name'
            );
        }
        return new SapRfcFunction($this->getConnection(), $name);
    }

    /**
     * Creates a connection using the underlying PHP module.
     * @throws \phpsap\exceptions\ConnectionFailedException
     */
    public function connect()
    {
        if (!$this->isConnected()) {
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
}
