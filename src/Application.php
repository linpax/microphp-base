<?php
/**
 * @link https://github.com/linpax/microphp-base
 * @copyright Copyright &copy; 2017 Linpax
 * @license https://github.com/linpax/microphp-base/blob/master/LICENSE
 */

namespace Micro\Base;


abstract class Application
{
    /** @var Kernel $kernel */
    private $kernel;
    /** @var mixed $request */
    private $request;


    abstract public function run($request);
    abstract public function exception($error);


    final public function __construct(Kernel $kernel)
    {
        $this->kernel = $kernel;
    }

    final public function getRequest()
    {
        return $this->request;
    }


    public function handle($request)
    {
        $this->kernel->loader($this->getConfig());

        try {
            return $this->run($request);
        } catch (\Exception $e) {
            if ($this->kernel->isDebug()) {
                throw $e;
            }

            return $this->exception($e);
        }
    }

    public function getConfig()
    {
        return require $this->kernel->getAppDir() . '/etc/index.php';
    }
}