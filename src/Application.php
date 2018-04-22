<?php declare(strict_types=1);
/**
 * @link https://github.com/linpax/microphp-base
 * @copyright Copyright &copy; 2017 Linpax
 * @license https://github.com/linpax/microphp-base/blob/master/LICENSE
 */

namespace Micro\Base;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;


interface Application {
    public function __construct(Kernel $kernel);
    public function run(RequestInterface $request) : ResponseInterface;
}