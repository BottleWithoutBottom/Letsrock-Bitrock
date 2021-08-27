<?php

namespace Bitrock\Utils\FileGenerator\Generator;

use Bitrock\Utils\FileGenerator\Prototypes\BitrixInfoblockPrototype;
use Bitrock\Utils\FileGenerator\Prototypes\ClassPrototype;
use Bitrock\Utils\FileGenerator\Stubs\BitrixInfoblockStub;
use Bitrock\Utils\FileGenerator\Stubs\ClassStub;
use Bitrock\LetsCore;

class BitrixInfoblockGenerator extends BitrixModelGenerator
{
    protected $generatedMode = false;
    protected $generatedPath;

    protected $symbolCodeStubs = [
        '{{symbolCode}}', '{{ symbolCode }}'
    ];

    protected $infoblockIdStubs = [
        '{{infoblockId}}', '{{ infoblockId }}'
    ];

    protected $bitrixPropertiesStubs = [
        '{{bitrixProperties}}', '{{ bitrixProperties }}'
    ];

    public function __construct(
        ClassPrototype $prototype,
        ClassStub $stub
    ) {
        parent::__construct($prototype, $stub);
        $this->path = LetsCore::getEnv(LetsCore::GENERATE_INFOBLOCK_MODELS_PATH);

        $this->generatedPath = $this->path
            . LetsCore::getEnv(LetsCore::GENERATE_INFOBLOCK_GENERATED_MODELS_DIR_NAME) . DIRECTORY_SEPARATOR;
    }

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

    protected function placeSymbolCode(
        BitrixInfoblockPrototype $infoblockPrototype
    ) {
        $symbolCode = $infoblockPrototype->getSymbolCode();

        if (!empty($symbolCode)) {
            foreach ($this->symbolCodeStubs as $symbolCodeStub) {
                if (strrpos($this->getStubString(), $symbolCodeStub)) {
                    $newStub = str_replace($symbolCodeStub, "'" . $symbolCode . "'", $this->getStubString());
                    $this->stubString = $newStub;
                    return true;
                }
            }
        }

        return false;
    }

    protected function placeBitrixProperties(
        BitrixInfoblockPrototype $prototype,
        BitrixInfoblockStub $stub
    ) {
        $properties = $prototype->getBitrixProperties();
        $propertiesCount = count($properties);
        if ($propertiesCount) {
            $resPropertiesString = '';
            $constsCount = 0;
            foreach ($properties as $propertyCode) {
                $disablePreString = !$constsCount;
                $disableLastString = !$constsCount + 1 >= $propertiesCount;
                $property = $this->createConst(
                    $propertyCode,
                    $propertyCode,
                    'public',
                    $disablePreString,
                    $disableLastString
                );
                $resPropertiesString .= $property;
                $constsCount++;
            }
            foreach ($this->bitrixPropertiesStubs as $bitrixPropertiesStub) {
                if (strrpos($this->getStubString(), $bitrixPropertiesStub)) {
                    $newStub = str_replace($bitrixPropertiesStub, $resPropertiesString, $this->getStubString());
                    $this->stubString = $newStub;
                    return true;
                }
            }

        } else {
            return $this->clearBitrixProperties($stub);
        }
        return false;
    }

    protected function placeInfoblockId(BitrixInfoblockPrototype $prototype)
    {
        $infoblockId = $prototype->getInfoblockId();

        if (!empty($infoblockId)) {
            foreach ($this->infoblockIdStubs as $infoblockIdStub) {
                if (strrpos($this->getStubString(), $infoblockIdStub)) {
                    $newStub = str_replace($infoblockIdStub, $infoblockId, $this->getStubString());
                    $this->stubString = $newStub;
                    return true;
                }
            }
        }

        return false;
    }

    protected function clearSymbolCode(BitrixInfoblockStub $stub)
    {
        foreach ($this->symbolCodeStubs as $symbolCodeStub) {
            if (strrpos($this->getStubString(), $symbolCodeStub)) {
                $symbolCodePropertyStub = $stub->getSymbolCodeStub();
                $newStub = str_replace($symbolCodePropertyStub, '', $this->getStubString());
                $this->stubString = $newStub;
                return true;
            }
        }

        return false;
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

    protected function clearBitrixProperties(BitrixInfoblockStub $stub)
    {
        $propertyStub = $stub->getBitrixProperiesStub();
        if (strrpos($this->getStubString(), $propertyStub)) {
            $newStub = str_replace($propertyStub, '', $this->getStubString());
            $this->stubString = $newStub;
        }

        return true;
    }

    public function getGeneratedFullFilePath()
    {
        if (empty($this->getFileName()) || empty($this->getGeneratedPath())) return false;

        return $this->getGeneratedPath() . $this->getFileName() . $this->ext;
    }

    public function getGeneratedPath()
    {
        return $this->generatedPath;
    }

    public function setGeneratedMode($mode)
    {
        $this->generatedMode = $mode;
    }

    public function getGeneratedMode()
    {
        return $this->generatedMode;
    }
}