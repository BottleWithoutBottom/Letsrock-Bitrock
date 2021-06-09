<?php
namespace Bitrock\EventHandlers;
use Bitrix\Main\EventManager;
use Bitrock\Events\IblockEvent;
use Bitrock\LetsCore;

class IblockHandler extends EventHandler
{
    public static function executeInfoblockModelsGeneration()
    {
        $onAddFlag = LetsCore::getEnv(LetsCore::GENERATE_ON_ADD);
        $onUpdateFlag = LetsCore::getEnv(LetsCore::GENERATE_ON_UPDATE);
        $onDeleteFlag = LetsCore::getEnv(LetsCore::GENERATE_ON_DELETE);

        $classNamespace = LetsCore::getEnv(LetsCore::GENERATE_INFOBLOCK_NAMESPACE);

        if (!empty($classNamespace)) {
            if (
                !empty($onAddFlag)
                || !empty($onUpdateFlag)
                || !empty($onDeleteFlag)
            ) {
                $eventManager = EventManager::getInstance();
                if ($eventManager) {
                    if ($onAddFlag) {
                        $eventManager->addEventHandler('iblock', 'OnAfterIBlockAdd' ,[IblockEvent::class, 'createModel']);
                    }

                    if ($onUpdateFlag) {
                        $eventManager->addEventHandler('iblock', 'OnAfterIBlockUpdate', [IblockEvent::class, 'createModel']);
                    }

                    if ($onDeleteFlag) {
                        $eventManager->addEventHandler('iblock', 'OnIBlockDelete', [IblockEvent::class, 'deleteModel']);
                    }
                }
            }
        }

        return true;
    }
}