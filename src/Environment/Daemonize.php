<?php
/**
 * Daemonize process using provided environment.
 *
 * @category  Kernel
 * @package   AlmaDo\PHP
 * @author    Eugene Belov <eugen.alter@gmail.com>
 * @copyright 2015 Eugene Belov
 * @license   MIT
 * @link      https://github.com/almadomundo
 */

namespace AlmaDo\PHP\Daemonize\Environment;

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
interface Daemonize
{
    /**
     * Set environment.
     *
     * @param Environment $env Environment.
     *
     * @return void
     */
    public function setEnvironment(Environment $env);

    /**
     * Run command as daemonized.
     *
     * @param string $mainCommand   Main command to run.
     * @param string $searchCommand Search command to check on collisions.
     *
     * @return bool
     */
    public function run($mainCommand, $searchCommand = null);
}