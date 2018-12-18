<?php
/**
 * File src/SapRfcConfigB.php
 *
 * Type B configuration.
 *
 * @package saprfc-koucky
 * @author  Gregor J.
 * @license MIT
 */

namespace phpsap\saprfc;

use phpsap\classes\AbstractConfigB;

/**
 * Class phpsap\saprfc\SapRfcConfigB
 *
 * Configure connection parameters for SAP remote function calls using load
 * balancing (type B).
 *
 * @package phpsap\saprfc
 * @author  Gregor J.
 * @license MIT
 */
class SapRfcConfigB extends AbstractConfigB
{
    use SapRfcConfigTrait;
}
