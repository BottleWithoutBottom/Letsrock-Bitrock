<?php
namespace Bitrock\EventHandlers;
use Bitrix\Main\EventManager;
use Bitrock\Events\IblockEvent;

class IblockHandler extends EventHandler
{
    public static function executeInfoblockModelsGeneration()
    {
        $eventManager = EventManager::getInstance();

        if ($eventManager) {
            $eventManager->addEventHandler('iblock', 'OnAfterIBlockAdd' ,[IblockEvent::class, 'createModel']);
            $eventManager->addEventHandler('iblock', 'OnAfterIBlockUpdate', [IblockEvent::class, 'createModel']);
            $eventManager->addEventHandler('iblock', 'OnIBlockDelete', [IblockEvent::class, 'deleteModel']);
        }

        return true;
    }
}