<?php

namespace Bitrock\Utils\FileGenerator\Stubs;

abstract class AbstractStub implements StubInterface
{
    protected $stub;

    public function getStub()
    {
        return $this->stub;
    }

    public function setStub($stub)
    {
        $this->stub = $stub;
        return true;
    }

    abstract public function generateStub(): string;
}