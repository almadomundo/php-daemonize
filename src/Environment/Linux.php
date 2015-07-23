<?php
/**
 * Environment implementation for Linux OS.
 *
 * @category  Kernel
 * @package   AlmaDo\PHP
 * @author    Eugene Belov <eugen.alter@gmail.com>
 * @copyright 2015 Eugene Belov
 * @license   MIT
 * @link      https://github.com/almadomundo
 */

namespace AlmaDo\PHP\Daemonize\Environment;

use AlmaDo\PHP\Daemonize\Environment\SignalHandler;
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
class Linux extends Environment
{
    const COMMAND_HOLDER = 'nohup';

    const OUTPUT_STDOUT = '/dev/null';

    const OUTPUT_STDERR = '/dev/null';

    /**
     * Get daemonize command.
     *
     * @param string $command Command.
     * @param string $runner  Runner for command, like /usr/bin/sh
     *
     * @return string
     */
    public function getDaemonizeCommand($command, $runner = '')
    {
        $holderCommand = $this->checkIfCommandExists($this->config['commandHolder'])
            ? $this->getBinaryRunner($this->config['commandHolder']). ' '
            : '';
        $runner = $runner ? $runner.' ' : '';
        return $holderCommand.$runner.$command. ' 1>>'.$this->config['stdoutOutput'].' 2>>'.$this->config['stderrOutput'].' &';
    }

    /**
     * Check if command exists.
     *
     * @param string $command Command.
     *
     * @return bool
     */
    public function checkIfCommandExists($command)
    {
        $result = shell_exec('which '.$command);
        return !empty($result);
    }

    /**
     * Check if command is running.
     *
     * @param string $command Command.
     *
     * @throws Exception
     *
     * @return bool
     */
    public function checkIfCommandIsRunning($command)
    {
        if (!$command) {
            return false;
        }
        $this->checkRequiredCommands(['ps', 'grep']);
        $command = $this->prepareGrepSearchWithExclusion($command);
        $result  = shell_exec('ps aux | grep '. $command);
        return !empty($result);
    }

    /**
     * Kill process by id.
     *
     * @param string $processId Process id.
     *
     * @throws Exception
     *
     * @return bool
     */
    public function killProcessById($processId)
    {
        $this->checkRequiredCommands(['kill']);

        return system('kill '.$processId);
    }

    /**
     * Find process ids by name.
     *
     * @param string $processName Process name or search string.
     *
     * @throws Exception
     *
     * @return array
     */
    public function findProcessIdsByName($processName)
    {
        $this->checkRequiredCommands(['ps', 'grep', 'awk']);

        $result = shell_exec('ps aux | grep '.$this->prepareGrepSearchWithExclusion($processName). ' | awk {print $2}');
        return explode(PHP_EOL, $result);
    }

    /**
     * Get binary path for runner (full path).
     *
     * @param string $binary Binary (short name).
     *
     * @return string
     */
    public function getBinaryRunner($binary)
    {
        if (!$this->checkIfCommandExists($binary)) {
            return null;
        }

        return trim(shell_exec('which '.$binary));
    }

    /**
     * Prepare search for grep command and exclude command itself.
     *
     * @param string $command Command.
     *
     * @return string
     */
    protected function prepareGrepSearchWithExclusion($command)
    {
        return '"['.$command[0].']'. substr($command, 1).'"';
    }

    /**
     * Get system-specific signal handler
     *
     * @return callable
     */
    protected function getSignalHandler()
    {
        return function (SignalHandler $handler, $signal) {
            if (SIGKILL != $signal) {
                //do not redefine SIGKILL, for consistency
                pcntl_sigprocmask(SIG_UNBLOCK, [$signal]);
                if (!pcntl_signal($signal, [$handler, 'handle'])) {
                    $this->logger->error('Failed to register system signal '.$signal.': '. pcntl_strerror(pcntl_get_last_error()));
                } else {
                    $this->logger->info('Registered system signal: '.$signal);
                }
            }
        };
    }
}