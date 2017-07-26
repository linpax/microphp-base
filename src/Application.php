<?php
/**
 * @link https://github.com/linpax/microphp-base
 * @copyright Copyright &copy; 2017 Linpax
 * @license https://github.com/linpax/microphp-base/blob/master/LICENSE
 */

namespace Micro\Base;


abstract class Application
{
    /** @var Container $container */
    private $container;


    abstract protected function run();
    abstract protected function exception($error);


    final public function __construct(Kernel $kernel)
    {
        $this->container = new Container($this->getConfig($kernel));
        $this->container->addInject('kernel', $kernel);
    }

    final public function getContainer()
    {
        return $this->container;
    }


    protected function getConfig(Kernel $kernel = null)
    {
        /** @var Kernel $kernel */
        $kernel = $kernel ?: $this->getContainer()->get('kernel');

        return require $kernel->getAppDir() . '/../etc/index.php';
    }

    public function handle($request)
    {
        $this->container->addInject('request', $request);

        try {
            return $this->run();
        } catch (\Exception $e) {
            if ($this->container->get('kernel')->isDebug()) {
                throw $e;
            }

            return $this->exception($e);
        }
    }
}