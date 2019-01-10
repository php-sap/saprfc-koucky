<?php
/**
 * File tests/helper/SaprfcMockFunctions.php
 *
 * Singleton container of SAPRFC functions.
 *
 * @package saprfc-koucky
 * @author  Gregor J.
 * @license MIT
 */

namespace tests\phpsap\saprfc\helper;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class tests\phpsap\saprfc\helper\SaprfcMockFunctions
 *
 * Singleton container of SAPRFC functions.
 *
 * @package tests\phpsap\saprfc\helper
 * @author  Gregor J.
 * @license MIT
 */
class SaprfcMockFunctions implements ContainerInterface
{
    /**
     * @var array of anonymous functions
     */
    private $functions;

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $name Identifier of the entry to look for.
     *
     * @throws NotFoundExceptionInterface  No entry was found for **this**
     *                                     identifier.
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     *
     * @return mixed Entry.
     */
    public function get($name)
    {
        if ($this->has($name)) {
            return $this->functions[$name];
        }
        throw new SaprfcMockFunctionNotFoundException(sprintf(
            'Cannot find function %s',
            $name
        ));
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an
     * exception. It does however mean that `get($id)` will not throw a
     * `NotFoundExceptionInterface`.
     *
     * @param string $name Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has($name)
    {
        return array_key_exists($name, $this->functions);
    }

    /**
     * Set an entry.
     * @param string   $name     function name
     * @param \Closure $function Anonymous function or closure.
     * @return \tests\phpsap\saprfc\helper\SaprfcMockFunctions
     */
    public function mock($name, $function)
    {
        if (!is_string($name) || empty(trim($name))) {
            throw new \InvalidArgumentException('Expect function name to be a string!');
        }
        if (!is_object($function) && ! $function instanceof \Closure) {
            throw new \InvalidArgumentException('Expect function to be closure!');
        }
        $this->functions[$name] = $function;
        return $this;
    }

    /**
     * Always returns the same instance.
     *
     * Singleton pattern taken from Stackoverflow ;-)
     * https://stackoverflow.com/questions/203336/creating-the-singleton-design-pattern-in-php5
     *
     * @return \tests\phpsap\saprfc\helper\SaprfcMockFunctions
     */
    public static function instance()
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new SaprfcMockFunctions();
        }
        return $instance;
    }

    /**
     * SaprfcMockFunctions constructor.
     */
    private function __construct()
    {
        if (extension_loaded('saprfc')) {
            throw new \RuntimeException('Extension saprfc is loaded. Cannot run test.');
        }
        $this->functions = [];
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'saprfcMockFunctionTemplates.php';
    }
}
