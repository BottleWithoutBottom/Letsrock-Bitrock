<?php

namespace Bitrock\Router;
use Bitrock\LetsCore;
use Bitrock\Models\Singleton;

abstract class Router extends Singleton
{

    abstract public function handle();

    public static function preHook()
    {
        return !empty(LetsCore::getEnv(LetsCore::BOOTSTRAP_MODE));
    }
}