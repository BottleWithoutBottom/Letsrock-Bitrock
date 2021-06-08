<?php

namespace Bitrock\Models;
use Bitrock\LetsCore;
use \CMain;
\Bitrix\Main\Loader::includeModule('main');


class Server extends Model
{
    private static $instance;

    private function __construct()
    {}

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

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function getFullServerName()
    {
        return CMain::IsHTTPS() ? "https://" : "http://" . $_SERVER['SERVER_NAME'];

    }
}