<?php
/**
 * Signal handler contract.
 *
 * @category  Kernel
 * @package   AlmaDo\PHP
 * @author    Eugene Belov <eugen.alter@gmail.com>
 * @copyright 2015 Eugene Belov
 * @license   MIT
 * @link      https://github.com/almadomundo
 */

namespace AlmaDo\PHP\Daemonize\Environment;

use AlmaDo\PHP\Daemonize\Environment;

/**
 * PHP version 5
 *
 * @category  Kernel
 * @package   AlmaDo\PHP
 * @author    Eugene Belov <eugen.alter@gmail.com>
 * @copyright 2015 Eugene Belov
 * @license   MIT
 * @link      https://github.com/almadomundo
 */
interface SignalHandler
{
    /**
     * Handle environment signal.
     *
     * @param string $signal Signal.
     *
     * @return void
     */
    public function handle($signal);
}