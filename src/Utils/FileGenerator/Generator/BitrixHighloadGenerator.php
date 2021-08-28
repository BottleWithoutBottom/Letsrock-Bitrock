<?php

namespace Bitrock\Utils\FileGenerator\Generator;

use Bitrock\Utils\FileGenerator\Exceptions\FileGeneratorException;
use Bitrock\Utils\FileGenerator\Prototypes\BitrixHighloadPrototype;
use Bitrock\Utils\FileGenerator\Stubs\BitrixHighloadStub;

class BitrixHighloadGenerator extends BitrixInfoblockGenerator
{
    use StubManagerTrait;

    protected $tableNameStubs = [
        '{{tableName}}', '{{ tableName }}'
    ];

    protected $highloadIdStubs = [
        '{{highloadId}}', '{{ highloadId }}'
    ];

    protected $bitrixUfFieldsStubs = [
        '{{bitrixUfFields}}', '{{ bitrixUfFields }}'
    ];

    public function generate(): bool
    {
        if (parent::generate()) {

            if ($this->getGeneratedMode()) {
                $this->placeSymbolCode($this->getPrototype());
                $this->placeInfoblockId($this->getPrototype());
                $this->placeBitrixProperties(
                    $this->getPrototype(),
                    $this->getStub()
                );
            } else {
                $this->clearSymbolCode($this->getStub());
                $this->clearInfoblockId($this->getStub());
                $this->clearBitrixProperties($this->getStub());
            }
            return true;
        }

        return false;
    }

    protected function placeTableName(
        BitrixHighloadPrototype $highloadPrototype
    ) {
        $tableName = $highloadPrototype->getTableName();

        if (empty($tableName)) {
            throw new FileGeneratorException('TableName was not found.');
        } else {
            foreach ($this->tableNameStubs as $stub) {
                if (strrpos($this->getStubString(), $stub)) {
                    $newStub = str_replace($stub, "'" . $tableName . "'", $this->getStubString());
                    $this->stubString = $newStub;
                    return true;
                }
            }
            throw new FileGeneratorException('TableName stub was not found');
        }

    }

    protected function placeUfFields(
        BitrixHighloadPrototype $prototype,
        BitrixHighloadStub $stub
    ) {
        $ufFields = $prototype->getBitrixUfFields();
        $fieldsCount = count($ufFields);
        if ($fieldsCount) {
            $resFieldsString = '';
            $constsCount = 0;
            foreach ($ufFields as $ufFieldKey) {
                $disablePreString = !$constsCount;
                $disableLastString = !$constsCount + 1 >= $fieldsCount;
                $ufField = $this->createConst(
                    $ufFieldKey,
                    $ufFieldKey,
                    'public',
                    $disablePreString,
                    $disableLastString
                );
                $resFieldsString .= $ufField;
                $constsCount++;
            }
            $this->stubString = $this->fillPlaceholder(
                $this->getStubString(),
                $this->bitrixUfFieldsStubs,
                $resFieldsString,
                'UF_FIELDS stub was not found.'
            );
            return true;
        } else {
            return $this->clearUfFields($stub);
        }
    }

    protected function placeHighloadId(BitrixHighloadPrototype $prototype)
    {
        $hlId = $prototype->getHighloadId();
        $this->stubString = $this->fillPlaceholder(
            $this->getStubString(),
            $this->highloadIdStubs,
            $hlId,
            'Highload id stub was not found.',
            'Highload id was not found.'
        );
    }

    protected function clearTableName(BitrixHighloadStub $stub)
    {
        $stringToRemove = $stub->getTableNameStub();
        $this->stubString = $this->clearPlaceholder(
            $this->getStubString(),
            $this->tableNameStubs,
            $stringToRemove
        );
    }

    protected function clearInfoblockId(BitrixInfoblockStub $stub)
    {
        foreach ($this->infoblockIdStubs as $infoblockIdStub) {
            if (strrpos($this->getStubString(), $infoblockIdStub)) {
                $infoblockIdPropertyStub = $stub->getInfoblockIdStub();
                $newStub = str_replace($infoblockIdPropertyStub, '', $this->getStubString());
                $this->stubString = $newStub;
                return true;
            }
        }

        return false;
    }

    protected function clearUfFields(BitrixHighloadStub $stub)
    {
        $propertyStub = $stub->getBitrixUfFields();
        if (strrpos($this->getStubString(), $propertyStub)) {
            $newStub = str_replace($propertyStub, '', $this->getStubString());
            $this->stubString = $newStub;
        }

        return true;
    }
}