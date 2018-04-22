<?php declare(strict_types=1);
/**
 * @link https://github.com/linpax/microphp-base
 * @copyright Copyright &copy; 2017 Linpax
 * @license https://github.com/linpax/microphp-base/blob/master/LICENSE
 */

namespace Micro\Base;


class Kernel
{
    /** @const string VERSION Version framework */
    const VERSION = '3.0';


    /** @var bool $debug Debug-mode flag */
    private $debug;
    /** @var string $environment Application environment */
    private $environment;
    /** @var float $startTime Time of start framework */
    private $startTime;


    public function __construct(string $environment, bool $debug)
    {
        $this->environment = $environment;
        $this->debug = $debug;

        ini_set('display_errors', (string)(integer)$this->debug);
        ini_set('log_errors', (string)(integer)$this->debug);

        if ($this->debug) {
            ini_set('error_reporting', (string)-1);
            $this->startTime = microtime(true);
        }
    }

    /**
     * Clone system
     */
    public function __clone()
    {
        if ($this->debug) {
            $this->startTime = microtime(true);
        }
    }

    public function getEnvironment() : string
    {
        return $this->environment;
    }

    public function getStartTime() : float
    {
        return $this->startTime;
    }

    public function getElapsedTime() : float
    {
        return microtime(true) - $this->startTime;
    }

    public function isDebug() : bool
    {
        return $this->debug;
    }
}