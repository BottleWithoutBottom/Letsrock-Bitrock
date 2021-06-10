<?php

namespace Bitrock;
use Dotenv\Dotenv;
use Bitrock\EventHandlers\IblockHandler;

/** Класс для конфигурации Bitrock */
class LetsCore
{
    public CONST VIEWS_PATH = 'VIEWS_PATH';
    public CONST SERVER_MODE = 'SERVER_MODE';

    public CONST GENERATE_INFOBLOCK_MODELS_MODE = 'GENERATE_INFOBLOCK_MODELS_MODE';
    public CONST GENERATE_INFOBLOCK_MODELS_PATH = 'GENERATE_INFOBLOCK_MODELS_PATH';
    public CONST GENERATE_INFOBLOCK_GENERATED_MODELS_PATH = 'GENERATE_INFOBLOCK_GENERATED_MODELS_PATH';
    public CONST GENERATE_INFOBLOCK_NAMESPACE = 'GENERATE_INFOBLOCK_NAMESPACE';
    public CONST GENERATE_ON_ADD = 'GENERATE_ON_ADD';
    public CONST GENERATE_ON_UPDATE = 'GENERATE_ON_UPDATE';
    public CONST GENERATE_ON_DELETE = 'GENERATE_ON_DELETE';

    public CONST RESIZES_STORAGE_PATH = 'RESIZES_STORAGE_PATH';

    public CONST BOOTSTRAP_MODE = 'BOOTSTRAP_MODE';
    public CONST BOOTSTRAP_URL = 'BOOTSTRAP_URL';
    public CONST BOOTSTRAP_PATH = 'BOOTSTRAP_PATH';

    public CONST DI_CONFIG_PATH = 'DI_CONFIG_PATH';

    /**  */
    public static function execute()
    {
        static::executeInfoblockModelsGeneration();
    }

    private static function executeInfoblockModelsGeneration()
    {
        $generationFlag = static::getEnv(static::GENERATE_INFOBLOCK_MODELS_MODE);
        $generationPath = static::getEnv(static::GENERATE_INFOBLOCK_MODELS_PATH);

        if (!empty($generationFlag) && !empty($generationPath)) {
            return IblockHandler::executeInfoblockModelsGeneration();
        }

        return false;
    }

    /**
     * Parse .env configuration
     *
     * @return bool
     */
    public static function parseConfiguration(string $envPath): bool
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