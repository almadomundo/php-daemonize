<?php
/**
 * Controller for daemons. It serves as a supervisor for other registered daemons.
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
class Controller implements DaemonService
{
    /**
     * @var Environment
     */
    protected $env;

    /**
     * @var array
     */
    protected  $config;

    /**
     * @var DaemonService[]
     */
    protected $daemons;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Constructor.
     *
     * @param Environment     $env     Environment.
     * @param array           $config  Configuration.
     * @param DaemonService[] $daemons Daemons to control.
     * @param LoggerInterface $logger  Logger.
     */
    public function __construct(Environment $env, array $config, array $daemons, LoggerInterface $logger)
    {
        $this->env     = $env;
        $this->config  = $config;
        $this->daemons = $daemons;
        $this->logger  = $logger;
    }

    /**
     * Run daemon.
     *
     * @return void
     */
    public function run()
    {
        //no fail-safe: this daemon is intended to be run as plain script, otherwise it might fail as well and it can not be controlled.
        $this->logger->info('Controller for daemons started');
        foreach ($this->daemons as $daemon) {
            if (!$daemon->isRunning()) {
                $this->logger->warning('Daemon "'.$daemon->getIdentifier().'" is not running, attempting to invoke');
                if ($invocation = $daemon->getInvocation()) {
                    $this->logger->info('Starting daemon "'.$daemon->getIdentifier().'" by command "'.$invocation.'"');
                    $this->env->runCliCommandDaemonize(
                        $daemon->getInvocation(),
                        $daemon->getIdentifier(),
                        $daemon->getBehavior()
                    );
                } else {
                    $this->logger->critical('Failed to start daemon "'.$daemon->getIdentifier().'" as it is configured wrong: no invoking command found');
                }
            }
        }
        $this->logger->info('Controller for daemons finished normally');
    }

    /**
     * Check if daemon is running.
     *
     * @return bool
     */
    public function isRunning()
    {
        return false;
    }

    /**
     * Get daemon invocation command.
     *
     * @return string
     */
    public function getInvocation()
    {
        return null;
    }

    /**
     * Get daemon identifier for environment.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return null;
    }

    /**
     * Get behavior on daemonize attempt.
     *
     * @return null
     */
    public function getBehavior()
    {
        return null;
    }
}