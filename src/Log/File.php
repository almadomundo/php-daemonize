<?php
/**
 * A brief implementation of logger interface that might be extended, for example, using Symfony loggers
 *
 * @category  Kernel
 * @package   AlmaDo\PHP
 * @author    Eugene Belov <eugen.alter@gmail.com>
 * @copyright 2015 Eugene Belov
 * @license   MIT
 * @link      https://github.com/almadomundo
 */

namespace AlmaDo\PHP\Daemonize\Log;

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
abstract class File implements LoggerInterface
{
    /**
     * @var string
     */
    protected $messageTemplate;

    /**
     * @var resource
     */
    protected $fileStream;

    /**
     * @var array
     */
    protected $replacements;

    /**
     * Constructor.
     *
     * @param resource $fileStream      File stream
     * @param string   $messageTemplate Message template.
     * @param array    $replacements    Replacements for template
     */
    public function __construct($fileStream, $messageTemplate = null, array $replacements = [])
    {
        if (empty($replacements)) {
            $replacements = array(
                '{date}'    => date('Y-m-d H:i:s'),
                '{message}' => null,
                '{pid}'     => posix_getpid(),
                '{ppid}'    => posix_getppid()
            );
        }
        $this->messageTemplate = $messageTemplate;
        $this->fileStream      = $fileStream;
        $this->replacements    = $replacements;
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed  $level   Level
     * @param string $message Message
     * @param array  $context Context
     *
     * @return null
     */
    public function log($level, $message, array $context = array())
    {
        $message = isset($this->messageTemplate) ? $this->replaceByTemplate($message) : $message;
        fwrite($this->fileStream, $message);
    }

    /**
     * Replace by template.
     *
     * @param string $message Message.
     *
     * @return string
     */
    protected function replaceByTemplate($message)
    {
        $replacements = array_merge($this->replacements, ['{message}' => $message]);

        return strtr($this->messageTemplate, $replacements);
    }

}