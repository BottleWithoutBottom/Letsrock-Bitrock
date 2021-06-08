<?php

namespace Bitrock\Utils\FileGenerator\Generator;

interface GeneratorInterface
{
    /** method generates all the file content */
    public function generate();

    /** method return final file structure */
    public function getStubString();

    public function placeFile($path, $content);

    public function setFileName(string $name);

    public function getFileName();

    /** returns a full file path ($this->path + $this->fileName + $this->ext) */
    public function getFullFilePath();
}