<?php
/**
 * Process daemonizing with behavior policy "Error". Error: if one or more copy of process is already running, raise error and do not invoke new copy
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
use AlmaDo\PHP\Daemonize\Environment\Exception;
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
class Error extends Daemonize implements DaemonizeInterface
{
    /**
     * Run command as daemonized.
     *
     * @param string $mainCommand   Main command to run.
     * @param string $searchCommand Search command to check on collisions.
     *
     * @throws Exception
     *
     * @return bool
     */
    public function run($mainCommand, $searchCommand = null)
    {
        $mainCommand   = $this->env->getDaemonizeCommand($mainCommand);
        $searchCommand = isset($searchCommand) ? $searchCommand : $mainCommand;
        $isRunning     = $this->env->checkIfCommandIsRunning($searchCommand);
        if ($isRunning) {
            $this->logger->error('Daemonize: error for command run "'.$mainCommand.'" as it is already running and policy is "Error on concurrent"');
            throw new Exception('Process '.$searchCommand. ' is already running');
        }

        return system($mainCommand);
    }
}