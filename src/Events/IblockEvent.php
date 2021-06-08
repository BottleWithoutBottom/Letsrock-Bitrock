<?php
namespace Bitrock\Events;

use Bitrock\Utils\FileGenerator\Generator\BitrixInfoblockGenerator;
use Bitrock\Utils\FileGenerator\Prototypes\BitrixInfoblockPrototype;
use Bitrock\Utils\FileGenerator\Stubs\BitrixInfoblockStub;
use Bitrock\Utils\FileGenerator\GenetratorCommand\BitrixInfoblockGeneratorCommand as BitInfoblockComm;
use Bitrock\Models\Infoblock\Generated\GeneratedInfoblockModel;
use Bitrock\Models\Infoblock\InfoblockModel;

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

            $infoblockModelReflection = new \ReflectionClass(new InfoblockModel());
            $generatedInfoblockModelReflection = new \ReflectionClass(new GeneratedInfoblockModel());
            $command->execute([
                BitInfoblockComm::IBLOCK_ID => $infoblockId,
                BitInfoblockComm::SYMBOL_CODE => $symbolCode,
                BitInfoblockComm::PROPERTIES => $properties,
                BitInfoblockComm::PARENT_CLASS_NAME => $infoblockModelReflection->getShortName(),
                BitInfoblockComm::NAMESPACE => $infoblockModelReflection->getNamespaceName(),
                BitInfoblockComm::PARENT_NAMESPACE => $infoblockModelReflection->getName(),
                BitInfoblockComm::GENERATED_NAMESPACE => $generatedInfoblockModelReflection->getNamespaceName(),
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