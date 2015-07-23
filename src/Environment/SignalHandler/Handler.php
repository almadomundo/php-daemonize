<?php
/**
 * Generic signal handler. Signals may be sent with kill command.
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
abstract class Handler implements SignalHandler
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Constructor.
     *
     * @param LoggerInterface $logger Logger.
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Handle environment signal.
     *
     * @param string $signal  Signal.
     * @param string $content Content to pass as an exit determinator.
     *
     * @return void
     */
    public function handle($signal, $content = null)
    {
        if ($content) {
            ob_start();
            echo($content);
        }

        exit;
    }
}