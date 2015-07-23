<?php
/**
 * Process daemonizing with behavior policy "Ignore". Ignore: if one or more copy of process is already running, leave them intact and invoke new copy
 *
 * @category  Kernel
 * @package   AlmaDo\PHP
 * @author    Eugene Belov <eugen.alter@gmail.com>
 * @copyright 2015 Eugene Belov
 * @license   MIT
 * @link      https://github.com/almadomundo
 */

namespace AlmaDo\PHP\Daemonize\Environment\Daemonize;

use AlmaDo\PHP\Daemonize\Environment;
use AlmaDo\PHP\Daemonize\Environment\Daemonize as DaemonizeInterface;


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
class Ignore extends Daemonize implements DaemonizeInterface
{
    /**
     * Run command as daemonized.
     *
     * @param string $mainCommand   Main command to run.
     * @param string $searchCommand Search command to check on collisions.
     *
     * @return bool
     */
    public function run($mainCommand, $searchCommand = null)
    {
        $mainCommand   = $this->env->getDaemonizeCommand($mainCommand);
        $searchCommand = isset($searchCommand) ? $searchCommand : $mainCommand;
        $isRunning     = $this->env->checkIfCommandIsRunning($searchCommand);
        if ($isRunning) {
            $this->logger->alert('Daemonize: run new instance of command "'.$mainCommand.'" and ignoring currently running instance as policy is "Ignore on concurrent"');
        }

        return system($mainCommand);
    }
}