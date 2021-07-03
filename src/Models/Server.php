<?php

namespace Bitrock\Models;
use Bitrock\LetsCore;
use Bitrock\Models\Singleton;


class Server extends Singleton
{
    public function isProduction()
    {
        return LetsCore::getEnv(LetsCore::SERVER_MODE) == 'prod';
    }

    public function isDev()
    {
        return LetsCore::getEnv(LetsCore::SERVER_MODE) == 'dev';
    }

    public function isLocal()
    {
        return LetsCore::getEnv(LetsCore::SERVER_MODE) == 'local';
    }

    public function getFullServerName()
    {
        return $this->isHttps() ? "https://" : "http://" . $_SERVER['SERVER_NAME'];
    }

    public function isHttps()
    {
        return $_SERVER['HTTPS'];
    }
}