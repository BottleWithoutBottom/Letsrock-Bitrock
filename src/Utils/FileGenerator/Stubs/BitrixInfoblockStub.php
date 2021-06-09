<?php

namespace Bitrock\Utils\FileGenerator\Stubs;

class BitrixInfoblockStub extends BitrixModelStub
{
    protected string $symbolCodeStub = "\n\tprotected" . ' $symbolCode ' . "= {{symbolCode}};";
    protected string $infoblockIdStub = "\n\tprotected" . ' $infoblockId ' . "= {{infoblockId}};";
    
    protected string $biitrixPropertiesStub = '
    
    /** PROPERTIES */
    {{bitrixProperties}}
    /** END PROPERTIES */
    
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
    $this->getBitrixProperiesStub()
    
    . $this->getSymbolCodeStub()
    . $this->getInfoblockIdStub() .
"\n}";
    }

    public function getBitrixProperiesStub(): string
    {
        return $this->biitrixPropertiesStub;
    }

    public function getSymbolCodeStub()
    {
        return $this->symbolCodeStub;
    }

    public function getInfoblockIdStub()
    {
        return $this->infoblockIdStub;
    }
}