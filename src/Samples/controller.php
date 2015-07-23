<?php
/**
 * CLI for supervisor daemon
 * However, this controller daemon itself can not be controlled by itself and therefore might be a subject for cron job.
 *
 * PHP version 5
 *
 * @category  Kernel
 * @package   AlmaDo\PHP
 * @author    Eugene Belov <eugen.alter@gmail.com>
 * @copyright 2015 Eugene Belov
 * @license   MIT
 * @link      https://github.com/almadomundo
 */

use AlmaDo\PHP\Daemonize\DaemonService;

/**
 * This is just a brief example of how to use daemon directly
 */


/** @var DaemonService $daemon <Add construction here>*/
$daemon->run();