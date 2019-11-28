<?php

namespace phpsap\saprfc;

use phpsap\classes\AbstractConnection;
use phpsap\classes\Config\ConfigCommon;
use phpsap\classes\Config\ConfigTypeA;
use phpsap\exceptions\ConfigKeyNotFoundException;
use phpsap\exceptions\ConnectionFailedException;
use phpsap\exceptions\IncompleteConfigException;
use phpsap\exceptions\UnknownFunctionException;
use phpsap\interfaces\Config\IConfigTypeA;
use phpsap\interfaces\Config\IConfigTypeB;

/**
 * Class phpsap\saprfc\SapRfcConnection
 *
 * PHP/SAP connection class abstracting connection related functions using
 * Eduard Kouckys saprfc module.
 *
 * @package phpsap\saprfc
 * @author  Gregor J.
 * @license MIT
 */
class SapRfcConnection extends AbstractConnection
{
    /**
     * Prepare a remote function call and return a function instance.
     * @param string $name
     * @return SapRfcFunction
     * @throws ConnectionFailedException
     * @throws UnknownFunctionException
     * @throws IncompleteConfigException
     */
    protected function createFunctionInstance($name)
    {
        return new SapRfcFunction($this->getConnection(), $name);
    }

    /**
     * Open a connection in case it hasn't been done yet and return the
     * connection resource.
     * @return resource
     * @throws ConnectionFailedException
     * @throws IncompleteConfigException
     */
    protected function getConnection()
    {
        /**
         * Open a new connection resource.
         */
        $connection = @saprfc_open($this->getModuleConfig());
        /**
         * In case the connection couldn't be opened, remove the resource and
         * throw an exception.
         */
        if ($connection === false) {
            unset($connection);
            throw new ConnectionFailedException(sprintf(
                'Connection creation failed: %s',
                @saprfc_error()
            ));
        }
        return $connection;
    }

    /**
     * Get the connection configuration.
     * @return array
     * @throws IncompleteConfigException
     */
    protected function getModuleConfig()
    {
        $common = $this->getCommonConfig();
        /**
         * Only type A and B configurations are supported by this module,
         * its common classes and its interface. Therefore, we do not
         * expect any other types here.
         */
        if ($this->configuration instanceof ConfigTypeA) {
            $specific = $this->getTypeAConfig();
        } else {
            $specific = $this->getTypeBConfig();
        }
        //Once we end up here, the configuration is complete.
        return array_merge($common, $specific);
    }

    /**
     * Get the common configuration for the saprfc module.
     *
     * I chose a "stupid" (and repetitive) way because it is more readable
     * and thus better maintainable for others than an "intelligent" way.
     *
     * @return array
     * @throws IncompleteConfigException
     */
    private function getCommonConfig()
    {
        $config = $missing = [];
        try {
            $config['CLIENT'] = $this->configuration->getClient();
        } catch (ConfigKeyNotFoundException $exception) {
            $missing[] = ConfigCommon::JSON_CLIENT;
        }
        try {
            $config['USER'] = $this->configuration->getUser();
        } catch (ConfigKeyNotFoundException $exception) {
            $missing[] = ConfigCommon::JSON_USER;
        }
        try {
            $config['PASSWD'] = $this->configuration->getPasswd();
        } catch (ConfigKeyNotFoundException $exception) {
            $missing[] = ConfigCommon::JSON_PASSWD;
        }
        if (!empty($missing)) {
            throw new IncompleteConfigException(sprintf(
                'Configuration is missing mandatory key(s) %s!',
                implode(', ', $missing)
            ));
        }
        try {
            $config['LANG']  = $this->configuration->getLang();
        } catch (ConfigKeyNotFoundException $exception) {
            //Do nothing, as these keys are not mandatory!
        }
        try {
            $config['TRACE'] = $this->configuration->getTrace();
        } catch (ConfigKeyNotFoundException $exception) {
            //Do nothing, as these keys are not mandatory!
        }
        return $config;
    }

    /**
     * Get the connection type A configuration for the saprfc module.
     *
     * I chose a "stupid" (and repetitive) way because it is more readable
     * and thus better maintainable for others than an "intelligent" way.
     *
     * @return array
     * @throws IncompleteConfigException
     */
    private function getTypeAConfig()
    {
        $config = $missing = [];
        try {
            $config['ASHOST'] = $this->configuration->getAshost();
        } catch (ConfigKeyNotFoundException $exception) {
            $missing[] = IConfigTypeA::JSON_ASHOST;
        }
        try {
            $config['SYSNR']  = $this->configuration->getSysnr();
        } catch (ConfigKeyNotFoundException $exception) {
            $missing[] = IConfigTypeA::JSON_SYSNR;
        }
        if (!empty($missing)) {
            throw new IncompleteConfigException(sprintf(
                'Configuration is missing mandatory key(s) %s!',
                implode(', ', $missing)
            ));
        }
        try {
            $config['GWHOST'] = $this->configuration->getGwhost();
        } catch (ConfigKeyNotFoundException $exception) {
            //Do nothing, as these keys are not mandatory!
        }
        try {
            $config['GWSERV'] = $this->configuration->getGwserv();
        } catch (ConfigKeyNotFoundException $exception) {
            //Do nothing, as these keys are not mandatory!
        }
        return $config;
    }

    /**
     * Get the connection type B configuration for the saprfc module.
     *
     * I chose a "stupid" (and repetitive) way because it is more readable
     * and thus better maintainable for others than an "intelligent" way.
     *
     * @return array
     * @throws IncompleteConfigException
     */
    private function getTypeBConfig()
    {
        $config = [];
        try {
            $config['MSHOST'] = $this->configuration->getMshost();
        } catch (ConfigKeyNotFoundException $exception) {
            throw new IncompleteConfigException(sprintf(
                'Configuration is missing mandatory key %s!',
                IConfigTypeB::JSON_MSHOST
            ));
        }
        try {
            $config['R3NAME'] = $this->configuration->getR3name();
        } catch (ConfigKeyNotFoundException $exception) {
            //Do nothing, as these keys are not mandatory!
        }
        try {
            $config['GROUP']  = $this->configuration->getGroup();
        } catch (ConfigKeyNotFoundException $exception) {
            //Do nothing, as these keys are not mandatory!
        }
        return $config;
    }
}
