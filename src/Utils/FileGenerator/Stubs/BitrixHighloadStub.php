<?php

namespace Bitrock\Utils\FileGenerator\Stubs;

class BitrixHighloadStub extends BitrixModelStub
{
    protected string $tableNameStub = "\n\tprotected" . ' $tableNameStub ' . "= {{tableName}};";
    protected string $highloadId = "\n\tprotected" . ' $highloadId ' . "= {{highloadId}};";

    protected string $bitrixUfFields = '
    
    /** UF_FIELDS */
    {{bitrixUfFields}}
    /** END UF_FIELDS */
    
    ';

    public function generateStub(): string
    {
        return
            "<?php
{{commentStub}}

namespace {{namespace}};
use {{parentNamespace}};

class {{class}} extends {{parentClass}}
{
" .
            $this->getBitrixUfFields()

            . $this->getTableNameStub()
            . $this->getHighloadIdStub() .
            "\n}";
    }

    public function getBitrixUfFields(): string
    {
        return $this->bitrixUfFields;
    }

    public function getTableNameStub()
    {
        return $this->tableNameStub;
    }

    public function getHighloadIdStub()
    {
        return $this->highloadId;
    }
}