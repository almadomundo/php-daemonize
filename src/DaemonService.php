<?php
/**
 * Service for daemon processes.
 *
 * @category  Kernel
 * @package   AlmaDo\PHP
 * @author    Eugene Belov <eugen.alter@gmail.com>
 * @copyright 2015 Eugene Belov
 * @license   MIT
 * @link      https://github.com/almadomundo
 */

namespace AlmaDo\PHP\Daemonize;


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
interface DaemonService
{
    /**
     * Run daemon.
     *
     * @return void
     */
    public function run();

    /**
     * Check if daemon is running.
     *
     * @return bool
     */
    public function isRunning();

    /**
     * Get daemon invocation command.
     *
     * @return string
     */
    public function getInvocation();

    /**
     * Get daemon identifier for environment.
     *
     * @return string
     */
    public function getIdentifier();

    /**
     * Get behavior on daemonize attempt.
     *
     * @return mixed
     */
    public function getBehavior();
}