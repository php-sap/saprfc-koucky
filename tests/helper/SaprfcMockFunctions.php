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

use kbATeam\MemoryContainer\Container;

/**
 * Class tests\phpsap\saprfc\helper\SaprfcMockFunctions
 *
 * Singleton container of SAPRFC functions.
 *
 * @package tests\phpsap\saprfc\helper
 * @author  Gregor J.
 * @license MIT
 */
class SaprfcMockFunctions extends Container
{
    /**
     * @var array Valid SAPRFC function names.
     */
    private static $validFunctionNames = [
        'saprfc_close',
        'saprfc_function_free',
        'saprfc_error',
        'saprfc_exception',
        'saprfc_open',
        'saprfc_function_discover',
        'saprfc_call_and_receive',
        'saprfc_function_interface',
        'saprfc_import',
        'saprfc_table_init',
        'saprfc_export',
        'saprfc_table_rows',
        'saprfc_table_read'
    ];

    /**
     * Add a function mock.
     * @param  string   $name     function name
     * @param  \Closure $function Anonymous function or closure.
     * @throws \InvalidArgumentException
     */
    public function mock($name, $function)
    {
        $nameValid = $this->validateId($name);
        if (!is_object($function) && ! $function instanceof \Closure) {
            throw new \InvalidArgumentException('Expect function to be closure!');
        }
        $this->set($nameValid, $function);
    }

    /**
     * Validate an ID for the other methods.
     * @param  mixed  $name  The function name to validate.
     * @return string
     * @throws \InvalidArgumentException The function name was no string or an empty
     *         string, or not in the list of templates.
     */
    protected function validateId($name)
    {
        $return = parent::validateId($name);
        if (!in_array($return, static::$validFunctionNames, true)) {
            throw new \InvalidArgumentException(sprintf(
                '%s function not defined in template.',
                $return
            ));
        }
        return $return;
    }

    /**
     * SaprfcMockFunctions constructor.
     */
    public function __construct()
    {
        if (extension_loaded('saprfc')) {
            throw new \RuntimeException('Extension saprfc is loaded. Cannot run tests using mockups.');
        }
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'saprfcMockFunctionTemplates.php';
    }
}
