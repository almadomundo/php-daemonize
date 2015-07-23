<?php
/**
 * Generic environment definitions entity.
 *
 * @category  Kernel
 * @package   AlmaDo\PHP
 * @author    Eugene Belov <eugen.alter@gmail.com>
 * @copyright 2015 Eugene Belov
 * @license   MIT
 * @link      https://github.com/almadomundo
 */

namespace AlmaDo\PHP\Daemonize;

use AlmaDo\PHP\Daemonize\Environment\SignalHandler;
use AlmaDo\PHP\Daemonize\Environment\Daemonize;
use AlmaDo\PHP\Daemonize\Environment\Exception;
use Psr\Log\LoggerInterface;


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
abstract class Environment
{
    const TYPE_LINUX = 'Linux';

    const TYPE_WINDOWS = 'Windows';

    const TYPE_MAC = 'Mac';

    const TYPE_FREEBSD = 'Freebsd';

    const RUN_CANCEL_ON_CONCURRENT = 0;

    const RUN_ERROR_ON_CONCURRENT = 1;

    const RUN_FORCE_ON_CONCURRENT = 2;

    const RUN_IGNORE_ON_CONCURRENT = 3;

    const BINARY_DEFAULT_PHP = 'php';

    const COMMAND_HOLDER = '';

    const OUTPUT_STDOUT = '';

    const OUTPUT_STDERR = '';

    /**
     * @var array
     */
    protected $config;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Daemonize[]
     */
    protected $daemonizeMap;

    /**
     * @var SignalHandler[]
     */
    protected $signalMap;

    /**
     * Constructor.
     *
     * @param LoggerInterface $logger       Logger.
     * @param Daemonize[]     $daemonizeMap Mapping for daemonize behaviors.
     * @param SignalHandler[] $signalMap    Signal handlers map.
     * @param array           $config       Config.
     */
    public function __construct(LoggerInterface $logger, array $daemonizeMap = [], array $signalMap = [], array $config = [])
    {
        if (!isset($config['commandHolder']) || !$this->checkIfCommandExists($config['commandHolder'])) {
            $config['commandHolder'] = static::COMMAND_HOLDER;
        }
        if (!isset($config['stdoutOutput'])) {
            $config['stdoutOutput'] = static::OUTPUT_STDOUT;
        }
        if (!isset($config['stderrOutput'])) {
            $config['stderrOutput'] = static::OUTPUT_STDERR;
        }
        $this->config       = $config;
        $this->logger       = $logger;
        $this->daemonizeMap = $daemonizeMap;
        $this->signalMap    = $signalMap;
    }

    /**
     * Get daemonize command.
     *
     * @param string $command Command.
     * @param string $runner  Runner for command, like /usr/bin/sh
     *
     * @return string
     */
    abstract public function getDaemonizeCommand($command, $runner = '');

    /**
     * Check if command exists.
     *
     * @param string $command Command.
     *
     * @return bool
     */
    abstract public function checkIfCommandExists($command);

    /**
     * Check if command is running.
     *
     * @param string $command Command.
     *
     * @return bool
     */
    abstract public function checkIfCommandIsRunning($command);

    /**
     * Kill process by id.
     *
     * @param string $processId Process id.
     *
     * @return bool
     */
    abstract public function killProcessById($processId);

    /**
     * Find process ids by name.
     *
     * @param string $processName Process name or search string.
     *
     * @return array
     */
    abstract public function findProcessIdsByName($processName);

    /**
     * Get binary path for runner (full path).
     *
     * @param string $binary Binary (short name).
     *
     * @return string
     */
    abstract public function getBinaryRunner($binary);

    /**
     * Get system-specific signal handler
     *
     * @return callable
     */
    abstract protected function getSignalHandler();

    /**
     * Set signal handlers, defined for this environment.
     *
     * @return void
     */
    public function setSignalHandlers()
    {
        $signals = array_keys($this->signalMap);
        foreach ($signals as $signal) {
            $this->setSignalHandler($signal);
        }
    }

    /**
     * Set specific signal handler.
     *
     * @param string $signal Signal.
     *
     * @return void
     */
    public function setSignalHandler($signal)
    {
        if ($handler = $this->getHandlerBySignal($signal)) {
            call_user_func_array($this->getSignalHandler(), [$handler, $signal]);
        }
    }

    /**
     * Get binary path for runner (full path).
     *
     * @param string $binary Binary (binary short name).
     *
     * @return string
     */
    public function getDefaultPhpRunner($binary = self::BINARY_DEFAULT_PHP)
    {
        return $this->getBinaryRunner($binary);
    }

    /**
     * Kill process by name.
     *
     * @param string $processName Process name or search string.
     *
     * @return bool
     */
    public function killProcessByName($processName)
    {
        $result = true;
        foreach ($this->findProcessIdsByName($processName) as $processId) {
            $result = $result && $this->killProcessById($processId);
        }

        return $result;
    }

    /**
     * Run command.
     *
     * @param string $command Command.
     *
     * @return void
     */
    public function runCliCommandRaw($command)
    {
        system($command);
    }

    /**
     * Daemonize & run command.
     *
     * @param string $mainCommand   Main command.
     * @param string $searchCommand Search command, equals to main by default.
     * @param int    $behavior      Behavior for running command.
     *
     * @throws Exception
     *
     * @return bool
     */
    public function runCliCommandDaemonize($mainCommand, $searchCommand = null, $behavior = self::RUN_IGNORE_ON_CONCURRENT)
    {
        $daemonizer = $this->getDaemonizeByBehavior($behavior);
        $daemonizer->setEnvironment($this);

        return $daemonizer->run($mainCommand, $searchCommand);
    }

    /**
     * Get environment type.
     *
     * @return string
     */
    public function getType()
    {
        $mapping = array(
            'Linux'   => self::TYPE_LINUX,
            'FreeBSD' => self::TYPE_FREEBSD,
            'WINNT'   => self::TYPE_WINDOWS,
            'Darwin'  => self::TYPE_MAC
        );
        if (isset($mapping[PHP_OS])) {
            return $mapping[PHP_OS];
        }

        return null;
    }

    /**
     * Check required commands.
     *
     * @param array $commands Commands.
     *
     * @throws Exception
     *
     * @return void
     */
    protected function checkRequiredCommands(array $commands)
    {
        foreach ($commands as $command) {
            if (!$this->checkIfCommandExists($command)) {
                $this->logger->error('Environment failed to find command "'.$command.'" required by internal invocation');
                throw new Exception('Required environment command "' . $command . '" was not found', Exception::COMMAND_NOT_FOUND);
            }
        }
    }

    /**
     * Get daemonizer by behavior.
     *
     * @param int $behavior Behavior.
     *
     * @return Daemonize
     */
    protected function getDaemonizeByBehavior($behavior)
    {
        if (isset($this->daemonizeMap[$behavior]) && $this->daemonizeMap[$behavior] instanceof Daemonize) {
            return $this->daemonizeMap[$behavior];
        }

        return null;
    }

    /**
     * Get signal handler by signal
     *
     * @param string $signal Behavior.
     *
     * @return SignalHandler
     */
    protected function getHandlerBySignal($signal)
    {
        if (isset($this->signalMap[$signal]) && $this->signalMap[$signal] instanceof SignalHandler) {
            return $this->signalMap[$signal];
        }

        return null;
    }
}