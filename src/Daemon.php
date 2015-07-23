<?php
/**
 * Common daemon implementation with fail-safe. Fail-safe: on error/signal etc, it will try to dispatch origin event and re-invoke itself if needed.
 *
 * @category  Kernel
 * @package   AlmaDo\PHP
 * @author    Eugene Belov <eugen.alter@gmail.com>
 * @copyright 2015 Eugene Belov
 * @license   MIT
 * @link      https://github.com/almadomundo
 */

namespace AlmaDo\PHP\Daemonize;

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
class Daemon implements DaemonService
{
    /**
     * Default cycle interruption time, microseconds
     */
    const DEFAULT_INTERRUPT_TIME = 100000;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var Environment
     */
    protected $env;

    /**
     * @var callable
     */
    protected $action;

    /**
     * @var LoggerInterface
     */
    protected $logger;


    /**
     * Constructor.
     *
     * @param Environment     $env    Environment for daemon.
     * @param array           $config Configuration.
     * @param ActionService   $action Action to daemonize.
     * @param LoggerInterface $logger Logger.
     */
    public function __construct(Environment $env, array $config, ActionService $action, LoggerInterface $logger)
    {
        if (!isset($config['interruptTime'])) {
            $config['interruptTime'] = self::DEFAULT_INTERRUPT_TIME;
        }
        $this->config = $config;
        $this->env    = $env;
        $this->action = $action;
        $this->logger = $logger;
    }

    /**
     * Run daemon.
     *
     * @return void
     */
    public function run()
    {
        $this->logger->info('Daemon "'.$this->getIdentifier().'" invoked by command "'.$this->getInvocation().'" with rethrow policy code ('.$this->getBehavior().')');
        $this->setFailSafeHandler();
        $this->setSignalHandler();
        $interruptTime = $this->getInterruptTime();
        while (1) {
            try {
                $this->action->run();
            } catch (\Exception $exception) {
                $this->logger->error('Daemon "'.$this->getIdentifier().'" got action exception: "'.$exception->getMessage().'"');
            }
            usleep($interruptTime);
        }
        $this->logger->info('Daemon "'.$this->getIdentifier().'" finished normally');
    }

    /**
     * Check if daemon is running.
     *
     * @return bool
     */
    public function isRunning()
    {
        return $this->env->checkIfCommandIsRunning($this->getSearchCommand());
    }

    /**
     * Get daemon invocation command.
     *
     * @return string
     */
    public function getInvocation()
    {
        if (isset($this->config['command']['main'])) {
            return $this->config['command']['main'];
        }

        return null;
    }

    /**
     * Get daemon identifier for environment.
     *
     * @return string
     */
    public function getIdentifier()
    {
        if (isset($this->config['command']['search'])) {
            return $this->config['command']['search'];
        }
        if (isset($this->config['command']['main'])) {
            return $this->config['command']['main'];
        }

        return null;
    }

    /**
     * Get behavior on daemonize attempt.
     *
     * @return int
     */
    public function getBehavior()
    {
        return isset($this->config['failSafe']['behavior'])
            ? $this->config['failSafe']['behavior']
            : Environment::RUN_CANCEL_ON_CONCURRENT;
    }

    /**
     * Get main daemon command.
     *
     * @return string
     */
    protected function getMainCommand()
    {
        if ($command = $this->getInvocation()) {
            return $command;
        }
        return $this->env->getDefaultPhpRunner() . ' ' . join(' ', $_SERVER['argv']);
    }

    /**
     * Get daemon search command.
     *
     * @return string
     */
    protected function getSearchCommand()
    {
        $mainCommand = $this->getMainCommand();
        return isset($this->config['command']['search'])
            ? $this->config['command']['search']
            : $mainCommand;
    }

    /**
     * Get interrupt time
     *
     * @return int
     */
    protected function getInterruptTime()
    {
        //do in msec?
        return $this->config['interruptTime'];
    }

    /**
     * Set fail safe handler.
     *
     * @return void
     */
    protected function setFailSafeHandler()
    {
        if (!isset($this->config['failSafe']['run']) || !$this->config['failSafe']['run']) {
            return ;
        }
        $mainCommand   = $this->getMainCommand();
        $searchCommand = $this->getSearchCommand();
        $behavior      = Environment::RUN_IGNORE_ON_CONCURRENT; //to ignore current copy

        register_shutdown_function(function() use ($mainCommand, $searchCommand, $behavior) {
                if ($noRethrow = ob_get_contents()) {
                    ob_end_clean();
                    exit;
                }
                $this->logger->alert('Daemon "'.$this->getIdentifier().'" was rethrown');
                $this->env->runCliCommandDaemonize($mainCommand, $searchCommand, $behavior);
            });
    }

    /**
     * Set signal handlers.
     *
     * @return void.
     */
    protected function setSignalHandler()
    {
        $this->env->setSignalHandlers();
    }
}