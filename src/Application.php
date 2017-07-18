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

    public function __construct(Kernel $kernel)
    {
        $this->kernel = $kernel;
    }

    abstract public function handle();
}