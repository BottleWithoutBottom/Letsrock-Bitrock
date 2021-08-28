<?php

namespace Bitrock\Utils\FileGenerator\Generator;

use Bitrock\Utils\FileGenerator\Prototypes\ClassPrototype;
use Bitrock\Utils\FileGenerator\Stubs\ClassStub;
use Bitrock\LetsCore;

class BitrixModelGenerator extends ClassGenerator
{
    protected $generatedMode = false;
    protected $generatedPath;

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