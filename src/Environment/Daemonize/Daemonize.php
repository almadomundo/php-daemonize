<?php
/**
 * Generic implementation for process daemonizing.
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
abstract class Daemonize implements DaemonizeInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Environment
     */
    protected $env;

    /**
     * Constructor.
     *
     * @param LoggerInterface $logger Logger.
     * @param Environment     $env    Environment.
     */
    public function __construct(LoggerInterface $logger, Environment $env = null)
    {
        $this->logger = $logger;
        $this->env    = $env;
    }

    /**
     * Set environment.
     *
     * @param Environment $env Environment.
     *
     * @return void
     */
    public function setEnvironment(Environment $env)
    {
        $this->env = $env;
    }
}