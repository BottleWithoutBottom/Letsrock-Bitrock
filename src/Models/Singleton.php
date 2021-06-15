<?php

namespace Bitrock\Models;

class Singleton
{
    private static $instances = [];

    private function __construct(){}
    private function __clone(){}

    public static function getInstance()
    {
        if (!static::preHook()) return false;

        $className = static::class;
        if (!isset(static::$instances[$className])) {
            static::$instances[$className] = new static;
        }

        return static::$instances[$className];
    }

    /** method to be inherited */
    public static function preHook()
    {
        return true;
    }
}