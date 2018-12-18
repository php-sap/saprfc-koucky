<?php
/**
 * File src/SapRfcConfigA.php
 *
 * Type A configuration.
 *
 * @package saprfc-koucky
 * @author  Gregor J.
 * @license MIT
 */

namespace phpsap\saprfc;

use phpsap\classes\AbstractConfigA;

/**
 * Class phpsap\saprfc\SapRfcConfigA
 *
 * Configure connection parameters for SAP remote function calls using a specific
 * SAP application server (type A).
 *
 * @package phpsap\saprfc
 * @author  Gregor J.
 * @license MIT
 */
class SapRfcConfigA extends AbstractConfigA
{
    use SapRfcConfigTrait;
}
