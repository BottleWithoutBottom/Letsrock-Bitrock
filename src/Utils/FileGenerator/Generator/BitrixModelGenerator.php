<?php

namespace Bitrock\Utils\FileGenerator\Generator;

use Bitrock\Utils\FileGenerator\Prototypes\ClassPrototype;
use Bitrock\Utils\FileGenerator\Stubs\ClassStub;
use Bitrock\LetsCore;

class BitrixModelGenerator extends ClassGenerator
{
    public function __construct(
        ClassPrototype $prototype,
        ClassStub $stub
    ) {
        parent::__construct($prototype, $stub);
        $this->path = LetsCore::getEnv(LetsCore::GENERATE_INFOBLOCK_MODELS_PATH);
    }

    public function generate(): bool
    {
        return parent::generate();
    }

    public function generateClear(): bool
    {
        if ($class = $this->getPrototype()->getClass()) {
            $this->placeClass($this->getPrototype());
            $this->setFileName($class);

            if ($namespace = $this->getPrototype()->getNamespace()) {
                $this->placeNamespace($namespace);

                $this->placeParentClass($this->getPrototype());
                $this->placeParentNamespace($this->getPrototype());
                return true;
            }
        }

        return false;
    }

}