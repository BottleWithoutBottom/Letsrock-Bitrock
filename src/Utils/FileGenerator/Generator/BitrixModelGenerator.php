<?php

namespace Bitrock\Utils\FileGenerator\Generator;

use Bitrock\Utils\FileGenerator\Prototypes\ClassPrototype;
use Bitrock\Utils\FileGenerator\Stubs\ClassStub;
use Bitrock\LetsCore;

class BitrixModelGenerator extends ClassGenerator
{
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