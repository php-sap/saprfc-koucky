<?php
/**
 * File src/SaprfcMockFunctionException.php
 *
 * DESCRIPTION
 *
 * @package saprfc-koucky
 * @author  Gregor J.
 * @license MIT
 */

namespace tests\phpsap\saprfc\helper;

use Psr\Container\ContainerExceptionInterface;

/**
 * Class tests\phpsap\saprfc\helper\SaprfcMockFunctionException
 *
 * DESCRIPTION
 *
 * @package tests\phpsap\saprfc\helper
 * @author  Gregor J.
 * @license MIT
 */
class SaprfcMockFunctionException extends \RuntimeException implements ContainerExceptionInterface
{
}
