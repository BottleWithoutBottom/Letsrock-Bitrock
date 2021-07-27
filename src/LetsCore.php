<?php

namespace Bitrock;
use Bitrock\EventHandlers\IblockHandler;
use Bitrock\Models\Singleton;
use DI\Container;
use DI\ContainerBuilder;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

/** Класс для конфигурации Bitrock */
class LetsCore extends Singleton
{
    public CONST SERVER_MODE = 'SERVER_MODE';

    public CONST GENERATE_INFOBLOCK_MODELS_MODE = 'GENERATE_INFOBLOCK_MODELS_MODE';
    public CONST GENERATE_INFOBLOCK_MODELS_PATH = 'GENERATE_INFOBLOCK_MODELS_PATH';
    public CONST GENERATE_INFOBLOCK_GENERATED_MODELS_DIR_NAME = 'GENERATE_INFOBLOCK_GENERATED_MODELS_DIR_NAME';
    public CONST GENERATE_INFOBLOCK_NAMESPACE = 'GENERATE_INFOBLOCK_NAMESPACE';
    public CONST GENERATE_ON_ADD = 'GENERATE_ON_ADD';
    public CONST GENERATE_ON_UPDATE = 'GENERATE_ON_UPDATE';
    public CONST GENERATE_ON_DELETE = 'GENERATE_ON_DELETE';

    public CONST RESIZES_STORAGE_PATH = 'RESIZES_STORAGE_PATH';

    public CONST BOOTSTRAP_MODE = 'BOOTSTRAP_MODE';
    public CONST BOOTSTRAP_URL = 'BOOTSTRAP_URL';
    public CONST BOOTSTRAP_PATH = 'BOOTSTRAP_PATH';

    public CONST DI_CONFIG_PATH = 'DI_CONFIG_PATH';

    public CONST EVENT_HANDLERS_PATH = 'EVENT_HANDLERS_PATH';
    public CONST EVENT_HANDLERS_MODE = 'EVENT_HANDLERS_MODE';

    public CONST LOG_NAME = 'LOG_NAME';
    public CONST LOG_PATH = 'LOG_PATH';
    public CONST LOG_LEVEL = 'LOG_LEVEL';

    public CONST DEFAULT_CONFIG_DIR = 'config';

    /** @var Container */
    protected $container;

    /** @return self  */
    public static function getInstance()
    {
        return parent::getInstance();
    }

    public function execute()
    {
        static::executeInfoblockModelsGeneration();
        static::executeEventHandlers();
    }

    public function executeContainer()
    {
        try {
            $defaultConfigPath = static::getConfigFile('DI.php');
            $additionalConfigPath = static::getEnv(static::DI_CONFIG_PATH);
            $defaultConfigArray = require($defaultConfigPath);
            $additionalConfigArray = [];

            if (!empty($defaultConfigPath) && file_exists($additionalConfigPath)) {
                $additionalConfigArray = require($additionalConfigPath);
            }
            $definitions = is_array($additionalConfigArray)
                ? array_merge($defaultConfigArray, $additionalConfigArray)
                : $defaultConfigPath;

            $builder = new ContainerBuilder();
            $builder->addDefinitions($definitions);
            $this->container = $builder->build();
        } catch (FileNotFoundException $e) {
            throw new FileNotFoundException('Cannot open DI default config file.');
        }
    }

    /** @return Container  */
    public function getContainer()
    {
        return $this->container;
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

    private static function executeEventHandlers()
    {
        $executeMode =  static::getEnv(static::EVENT_HANDLERS_MODE);

        if ($executeMode) {
            $handlersPath = static::getEnv(static::EVENT_HANDLERS_PATH);

            $fileSystem = new Filesystem();

            if ($fileSystem->exists($handlersPath)) {
                require $handlersPath;
                return true;
            }
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
        return LetsEnv::getInstance()->parseConfiguration($envPath);
    }

    public static function getEnv($name)
    {
        return LetsEnv::getInstance()->getEnv($name);

    }

    public static function getRootDir()
    {
        return __DIR__;
    }

    public static function getConfigDir()
    {
        return static::getRootDir() . DIRECTORY_SEPARATOR . static::DEFAULT_CONFIG_DIR . DIRECTORY_SEPARATOR;
    }

    public static function getConfigFile($fileName)
    {
        if (empty($fileName)) return false;

        return static::getConfigDir() . $fileName;
    }
}