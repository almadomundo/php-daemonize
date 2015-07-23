<?php
/**
 * Service for daemon actions. It defines "what" daemon should do (not "how" as that logic is part of daemonizing flow).
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
interface ActionService
{
    /**
     * Run action.
     *
     * @return void
     */
    public function run();
}