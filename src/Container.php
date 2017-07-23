<?php
/**
 * @link https://github.com/linpax/microphp-base
 * @copyright Copyright &copy; 2017 Linpax
 * @license https://github.com/linpax/microphp-base/blob/master/LICENSE
 */

namespace Micro\Base;

// IoC Container
class Container
{
    /** @var array $config Configuration */
    protected $config = [];
    /** @var array $injects Configured injects */
    protected $injects = [];


    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function param($name)
    {
        return array_key_exists($name, $this->config) ? $this->config[$name] : null;
    }
    public function get($name)
    {
        if (!empty($this->config[$name])) {
            return $this->config[$name];
        }
        if (!empty($this->injects[$name])) {
            return $this->injects[$name];
        }
        if (!empty($this->config['components'][$name])) {
            return $this->loadInject($name);
        }

        return false;
    }

    public function addInject($name, $inject)
    {
        if (is_object($inject)) {
            $this->injects[$name] = $inject;
        } else {
            $this->config[$name] = $inject;
        }
    }
    public function loadInject($name)
    {
        // DEPENDENCY INJECTION
        $options = $this->config['components'][$name];
        $className = $options['class'];

        $options['arguments'] = !empty($options['arguments']) ? $this->buildParams($options['arguments']) : [];
        $options['property'] = !empty($options['property']) ? $this->buildParams($options['property']) : [];
        $options['calls'] = !empty($options['calls']) ? $this->buildCalls($options['calls']) : [];

        /** Depends via construction */
        $this->injects[$name] = $this->makeObject($className, $options['arguments']);
        if (!$this->injects[$name]) {
            return false;
        }

        /** Depends via property */
        if (!empty($options['property'])) { // load properties
            foreach ($options['property'] as $property => $value) {
                if (property_exists($this->injects[$name], $property)) {
                    $this->injects[$name]->$property = $value;
                }
            }
        }

        /** Depends via calls */
        if (!empty($options['calls'])) { // run methods
            foreach ($options['calls'] as $method => $arguments) {
                if (method_exists($this->injects[$name], $method)) {
                    $reflectionMethod = new \ReflectionMethod($className, $method);

                    if ($reflectionMethod->getNumberOfParameters() === 0) {
                        $this->injects[$name]->$method();
                    } else {
                        call_user_func_array([$this->injects[$name], $method], $arguments);
                    }
                }
            }
        }

        return $this->injects[$name];
    }

    private function buildParams(array $params)
    {
        foreach ($params AS $key => &$val) { // IoC Constructor
            if (is_string($params[$key]) && ('@' === $val[0])) {
                $val = $val === '@this' ? $this : $this->get(substr($val, 1));
            }
        }

        return $params;
    }
    private function buildCalls(array $params)
    {
        $callers = [];

        if (!is_array($params[0])) {
            $params = [ $params ];
        }

        foreach ($params as $arguments) {
            if (is_string($arguments[0])) {
                $callers[$arguments[0]] = !empty($arguments[1]) && is_array($arguments[1]) ? $this->buildParams($arguments[1]) : null;
            }
        }

        return $callers;
    }
    private function makeObject($className, array $arguments = [])
    {
        try {
            $reflection = new \ReflectionClass($className);
            $reflectionMethod = new \ReflectionMethod($className, '__construct');

            return $reflectionMethod->getNumberOfParameters() === 0 ? new $className:  $reflection->newInstanceArgs($arguments);
        } catch (\Exception $e) {
            return false;
        }
    }
}