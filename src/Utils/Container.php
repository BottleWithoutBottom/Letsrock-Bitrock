<?php

namespace Bitrock\Utils;
use \DI\ContainerBuilder;

class Container
{
    public CONST CURRENT_CLASS = 'CURRENT_CLASS';
    public CONST CURRENT_METHOD = 'CURRENT_METHOD';

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
                    $containerParams[] = new $paramNamespace();
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
        $this->builder->addDefinitions(DI_CONFIG_PATH);
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
    }
}