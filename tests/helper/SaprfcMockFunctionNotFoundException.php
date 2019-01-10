<?php
/**
 * File tests/helper/SaprfcMockFunctionNotFoundException.php
 *
 * DESCRIPTION
 *
 * @package saprfc-koucky
 * @author  Gregor J.
 * @license MIT
 */

namespace tests\phpsap\saprfc\helper;

use Psr\Container\NotFoundExceptionInterface;

/**
 * Class tests\phpsap\saprfc\helper\SaprfcMockFunctionNotFoundException
 *
 * DESCRIPTION
 *
 * @package tests\phpsap\saprfc\helper
 * @author  Gregor J.
 * @license MIT
 */
class SaprfcMockFunctionNotFoundException extends SaprfcMockFunctionException implements NotFoundExceptionInterface
{
}
