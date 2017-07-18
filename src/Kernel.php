<?php
/**
 * @link https://github.com/linpax/microphp-base
 * @copyright Copyright &copy; 2017 Linpax
 * @license https://github.com/linpax/microphp-base/blob/master/LICENSE
 */

namespace Micro\Base;


class Kernel
{
    /** @const string VERSION Version framework */
    const VERSION = '2.0';

    /** @var string $appDir */
    protected $appDir;

    /** @var bool $debug Debug-mode flag */
    private $debug = true;
    /** @var string $environment Application environment */
    private $environment = 'devel';
    /** @var float $startTime Time of start framework */
    private $startTime;

    /** @var bool $loaded Micro loaded flag */
    private $loaded;


    public function __construct($environment='devel', $debug=true)
    {
        $this->environment = (string)$environment;
        $this->debug = (bool)$debug;
        $this->loaded = false;

        ini_set('display_errors', (integer)$this->debug);
        ini_set('log_errors', (integer)$this->debug);

        if ($this->debug) {
            ini_set('error_reporting', -1);
            $this->startTime = microtime(true);
        }
    }
    public function terminate($request, $response)
    {
        //
    }

    /**
     * Clone kernel
     *
     * @access public
     *
     * @return void
     */
    public function __clone()
    {
        if ($this->debug) {
            $this->startTime = microtime(true);
        }

        $this->loaded = false;
    }

    /**
     * @return mixed
     */
    public function getConfig()
    {
        return $this->getAppDir().'/configs/index.php';
    }

    /**
     * @return mixed
     */
    public function getAppDir()
    {
        if (!$this->appDir) {
            $this->appDir = dirname((new \ReflectionObject($this))->getFileName());
        }

        return $this->appDir;
    }

    /**
     * @return bool
     */
    public function isDebug()
    {
        return $this->debug;
    }

    /**
     * @return bool
     */
    public function isCli()
    {
        return PHP_SAPI === 'cli';
    }

    /**
     * @return mixed
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    public function getCacheDir()
    {
        return $this->getAppDir().'/cache/'.$this->getEnvironment();
    }

    /**
     * @return mixed
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    public function getLogDir()
    {
        return $this->getAppDir().'/logs';
    }
}