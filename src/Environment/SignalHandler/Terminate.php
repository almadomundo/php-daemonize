<?php
/**
 * Signal handler for "terminate" event. Usually it is SIGTERM, kill -15 event. It is good idea to leave this signal intact.
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
class Terminate extends Handler implements SignalHandler
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
        $this->logger->alert('Process was terminated');

        parent::handle($signal, get_class($this));
    }
}