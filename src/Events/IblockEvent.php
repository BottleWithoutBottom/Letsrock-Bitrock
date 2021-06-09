<?php
namespace Bitrock\Events;

use Bitrock\Utils\FileGenerator\Generator\BitrixInfoblockGenerator;
use Bitrock\Utils\FileGenerator\Prototypes\BitrixInfoblockPrototype;
use Bitrock\Utils\FileGenerator\Stubs\BitrixInfoblockStub;
use Bitrock\Utils\FileGenerator\GenetratorCommand\BitrixInfoblockGeneratorCommand as BitInfoblockComm;
use Bitrock\Models\Infoblock\InfoblockModel;
use Bitrock\LetsCore;

class IblockEvent extends Event
{
    public function createModel($arFields)
    {
        $symbolCode = $arFields['CODE'];

        if (!empty($symbolCode)) {
            $infoblockId = $arFields['ID'];

            $properties = IblockEvent::getPropertiesSymbolCodes($infoblockId);
            $command = new BitInfoblockComm(
                new BitrixInfoblockStub(),
                new BitrixInfoblockPrototype(),
                BitrixInfoblockGenerator::class
            );

            $namespace = LetsCore::getEnv(LetsCore::GENERATE_INFOBLOCK_NAMESPACE);
            $generatedNamespace = $namespace . '\\' . LetsCore::getEnv(LetsCore::GENERATE_INFOBLOCK_GENERATED_MODELS_PATH);
            $infoblockModelReflection = new \ReflectionClass(new InfoblockModel());
            $command->execute([
                BitInfoblockComm::IBLOCK_ID => $infoblockId,
                BitInfoblockComm::SYMBOL_CODE => $symbolCode,
                BitInfoblockComm::PROPERTIES => $properties,
                BitInfoblockComm::PARENT_CLASS_NAME => $infoblockModelReflection->getShortName(),
                BitInfoblockComm::NAMESPACE => $namespace,
                BitInfoblockComm::PARENT_NAMESPACE => $infoblockModelReflection->getName(),
                BitInfoblockComm::GENERATED_NAMESPACE => $generatedNamespace,
            ]);
        }
    }

    public function deleteModel($ID)
    {
        var_dump($ID);
    }

    private static function getPropertiesSymbolCodes($infoblockId)
    {
        if (empty($infoblockId)) return [];

        $properties = \CIBlockProperty::GetList(
            ['ID' => 'ASC'],
            [
                'ACTIVE' => 'Y',
                'IBLOCK_ID' => $infoblockId
            ],
        );

        $res = [];
        while ($propRow = $properties->GetNext()) {
            $res[] = $propRow['CODE'];
        }

        return $res;
    }
}