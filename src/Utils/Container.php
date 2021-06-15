<?php

namespace Bitrock\Utils;
use \DI\ContainerBuilder;
use Bitrock\LetsCore;
use Bitrock\Models\Singleton;

class Container
{
    protected $builder;

    public function __construct() {
        $this->builder = new ContainerBuilder();
    }

    /** Метод достает параметры из метода и аргументы, и превращает их в единый массив
     * @param array $methodParams
     * @param array $params
     * @return array
     */
    public function resolveArguments(array $methodParams, array $params): array
    {
        if (empty($methodParams) && empty($params)) return [];
        $containerParams = [];
        foreach ($methodParams as $methodParam) {
            $paramType = $methodParam->getType();
            if (
                !empty($paramType)
                && $paramType->getName() !== null
            ) {
                try {
                    $paramNamespace = $methodParam->getType()->getName();
                    if ($this->checkSingletonByClassName($paramNamespace)) {
                        $containerParams[] = $paramNamespace::getInstance();
                    } else {
                        $containerParams[] = new $paramNamespace();
                    }
                } catch (\ReflectionException $e) {
                    die($e->getMessage());
                }
            }
        }

        if (count($params)) {
            $containerParams = array_merge($containerParams, $params);
        }

        return $containerParams;
    }

    public function handle($controllerArray, $args)
    {
        if (empty($controllerArray[0] || empty($controllerArray[1]))) return false;

        $class = $controllerArray[0];
        $method = $controllerArray[1];
        try {
            $this->builder->addDefinitions($this->getConfig());
            $instance = $this->builder->build();

            try {
                $reflectionClass = new \ReflectionClass($class);
                $currentMethod = $reflectionClass->getMethod($method);
                $currentMethodParams = $currentMethod->getParameters();
                $params = $this->resolveArguments($currentMethodParams, $args);
                return $instance->call([$class, $method], $params);
            } catch(\ReflectionException $e) {
                die($e->getMessage());
            }
        } catch(\Exception $e) {
            die('DI_CONFIG_PATH file was not found in config');
        }
    }

    private function checkSingletonByClassName($className)
    {
        if (empty($className)) return false;

        $reflection = new \ReflectionClass($className);
        return $reflection->isSubclassOf(Singleton::class);
    }

    private function getConfig()
    {
        $defaultConfigArrayPath = LetsCore::getConfigFile('DI.php');
        $customConfigArrayPath = LetsCore::getEnv(LetsCore::DI_CONFIG_PATH);
        $configArray = [];

        if (file_exists($defaultConfigArrayPath)) {
            $configArray = require($defaultConfigArrayPath);
        }

        if (file_exists($customConfigArrayPath)) {
            $customConfigArray = require($customConfigArrayPath);

            if (is_array($customConfigArray)) {
                $configArray = array_merge($configArray, $customConfigArray);
            }
        }

        return $configArray;

    }
}