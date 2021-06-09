<?php

namespace Bitrock\Router;
use Bitrock\LetsCore;

abstract class Router
{
    protected static $instance;

    private function __construct() {}

    public static function getInstance()
    {
        if (empty(LetsCore::getEnv(LetsCore::BOOTSTRAP_MODE))) return false;

        if (is_null(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    abstract public function handle();
}