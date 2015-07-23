<?php
/**
 * Signal handler for "restart" event. This is user-defined signal number.
 *
 * @category  Kernel
 * @package   AlmaDo\PHP
 * @author    Eugene Belov <eugen.alter@gmail.com>
 * @copyright 2015 Eugene Belov
 * @license   MIT
 * @link      https://github.com/almadomundo
 */

namespace AlmaDo\PHP\Daemonize\Environment\SignalHandler;

use AlmaDo\PHP\Daemonize\Environment\SignalHandler;

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
class Restart extends Handler implements SignalHandler
{
    /**
     * Handle environment signal.
     *
     * @param string $signal Signal.
     *
     * @return void
     */
    public function handle($signal)
    {
        $this->logger->alert('Process was restarted');

        parent::handle($signal);
    }
}