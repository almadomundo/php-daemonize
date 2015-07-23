<?php
/**
 * Signal handler for "halt" event. Usually it is SIGKILL, kill -9 event. Overriding this behaviour may lead to glitches in daemonize flow.
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
class Halt extends Handler implements SignalHandler
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
        $this->logger->alert('Process was killed');

        parent::handle($signal, get_class($this));
    }
}