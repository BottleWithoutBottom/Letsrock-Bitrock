<?php

namespace Bitrock\Models;

class Singleton
{
    private static $instance;

    private function __construct(){}
    private function __clone(){}

    public static function getInstance()
    {
        if (!static::preHook()) return false;

        if (is_null(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /** method to be inherited */
    public static function preHook()
    {
        return true;
    }
}