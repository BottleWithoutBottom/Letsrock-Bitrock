<?php

namespace Bitrock;
use Dotenv\Dotenv;
use Bitrock\EventHandlers\IblockHandler;

/** Класс для конфигурации Bitrock */
class LetsCore
{
    public CONST VIEWS_PATH = 'VIEWS_PATH';
    public CONST SERVER_MODE = 'SERVER_MODE';
    public CONST GENERATE_INFOBLOCK_MODELS = 'GENERATE_INFOBLOCK_MODELS';
    public CONST RESIZES_STORAGE_PATH = 'RESIZES_STORAGE_PATH';
    public CONST BOOTSTRAP_MODE = 'BOOTSTRAP_MODE';

    /**  */
    public static function execute()
    {
        static::executeInfoblockModelsGeneration();
    }

    private static function executeInfoblockModelsGeneration()
    {
        $generationFlag = static::getEnv(static::GENERATE_INFOBLOCK_MODELS);

        if (!empty($generationFlag)) {
            return IblockHandler::executeInfoblockModelsGeneration();
        }

        return false;
    }

    /**
     * Parse .env configuration
     *
     * @return bool
     */
    public static function parseLogConfiguration(string $envPath): bool
    {
        if (empty($envPath)) return false;

        $config = Dotenv::createImmutable($envPath);
        $config->load();

        return true;
    }

    public static function getEnv($name)
    {
        return $_ENV[$name];
    }
}