<?php

namespace Bitrock\Models;

class Singleton
{
    private static $instance;

    private function __construct(){}
    private function __clone(){}

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }
}