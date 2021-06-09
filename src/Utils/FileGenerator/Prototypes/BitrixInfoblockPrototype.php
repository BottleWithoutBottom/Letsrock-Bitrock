<?php

namespace Bitrock\Utils\FileGenerator\Prototypes;

class BitrixInfoblockPrototype extends BitrixModelPrototype
{
    protected string $symbolCode = '';
    protected int $infoblockId;
    protected array $bitrixProperties;

    /**
     * @return int
     */
    public function getInfoblockId(): int
    {
        return $this->infoblockId;
    }

    /**
     * @param int $infoblockId
     */
    public function setInfoblockId(int $infoblockId): void
    {
        $this->infoblockId = $infoblockId;
    }

    /**
     * @return string
     */
    public function getSymbolCode(): string
    {
        return $this->symbolCode;
    }

    /**
     * @param string $symbolCode
     */
    public function setSymbolCode(string $symbolCode): void
    {
        $this->symbolCode = $symbolCode;
    }

    public function setClassNameBySymbolCode()
    {
        if (!empty($this->getSymbolCode())) {
            $fileName = mb_strtolower($this->getSymbolCode());

            $fileName = ucfirst(preg_replace('#^ib_#', '', $fileName));
            $fileName = $this->checkCamel($fileName);
            return $this->setClass($fileName);
        }

        return false;
    }

    /**
     * @return array
     */
    public function getBitrixProperties(): array {
        return $this->bitrixProperties;
    }

    /**
     * @param array $bitrixProperties
     */
    public function setBitrixProperties(array $bitrixProperties): void {
        $this->bitrixProperties = $bitrixProperties;
    }

    private function checkCamel($string, $symbol = '_')
    {
        if ($position = strpos($string, $symbol)) {
            $firstPart = substr($string, 0, $position);
            $secondPart = ucfirst(substr($string, $position + 1));
            $newString = $firstPart . $secondPart;
            return $this->checkCamel($newString);
        }
        return $string;
    }
}