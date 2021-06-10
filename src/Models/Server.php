<?php

namespace Bitrock\Models;
use Bitrock\LetsCore;
use \CMain;
\Bitrix\Main\Loader::includeModule('main');


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
        return CMain::IsHTTPS() ? "https://" : "http://" . $_SERVER['SERVER_NAME'];

    }
}