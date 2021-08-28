<?php

namespace Bitrock\Utils\FileGenerator\Prototypes;

use Bitrock\Utils\FileGenerator\Exceptions\FileGeneratorPrototypeException;

class BitrixHighloadPrototype extends BitrixModelPrototype
{
    protected string $tableName = '';
    protected string $name = '';
    protected int $highloadId;
    protected array $bitrixUfFields;

    /**
     * @return int
     */
    public function getHighloadId(): int
    {
        return $this->highloadId;
    }

    /**
     * @param int $infoblockId
     */
    public function setHighloadId(int $highloadId): void
    {
        $this->highloadId = $highloadId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setClassNameByName()
    {
        if (!empty($this->getName())) {
            return $this->setClass($this->getName());
        }

        return false;
    }

    public function getTableName(): string
    {
        if (empty($this->tableName)) {
            throw new FileGeneratorPrototypeException('Tablename is not set');
        }

        return $this->tableName;
    }

    public function setTableName(string $tableName): void
    {
        $this->tableName = $tableName;
    }

    /**
     * @return array
     */
    public function getBitrixUfFields(): array {
        return $this->bitrixUfFields;
    }

    /**
     * @param array $bitrixUfFields
     */
    public function setBitrixUfFields(array $bitrixUfFields): void {
        $this->bitrixUfFields = $bitrixUfFields;
    }
}