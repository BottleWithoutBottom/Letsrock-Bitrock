<?php

namespace Bitrock\Utils\FileGenerator\GenetratorCommand;

use Bitrock\Models\Infoblock\InfoblockModel;
use Bitrock\LetsCore;

class BitrixInfoblockGeneratorCommand extends AbstractGeneratorCommand
{
    public CONST SYMBOL_CODE = 'symbolCode';
    public CONST IBLOCK_ID = 'iblockId';
    public CONST NAMESPACE = 'namespace';
    public CONST PROPERTIES = 'properties';
    public CONST PARENT_NAMESPACE = 'parentNamespace';
    public CONST PARENT_CLASS_NAME = 'parentClass';
    public CONST GENERATED_NAMESPACE = 'generatedNamespace';
    public CONST GENERATED_PARENT_CLASS_NAME = 'generatedParentClass';
    public CONST GENERATED_PARENT_NAMESPACE = 'generatedParentNamespace';
    public CONST GENERATED_CLASS_NAME = 'Generated';

    public function execute($params)
    {
        if ($this->initGeneratedPrototype($params)) {
            if ($this->generator->generate()) {
                if (
                $this->generator->placeFile(
                    $this->generator->getGeneratedFullFilePath(),
                    $this->generator->getStubString()
                )
                ) {
                    $params[static::GENERATED_PARENT_CLASS_NAME] = $this->prototype->getClass();
                    $params[static::GENERATED_PARENT_NAMESPACE] = $this->prototype->getNamespace() . '\\' . $this->prototype->getClass();
                    if ($this->initPrototype($params)) {
                        if ($this->generator->generate()) {
                            $this->generator->placeFile(
                                $this->generator->getFullFilePath(),
                                $this->generator->getStubString()
                            );
                        }
                    }
                }

            }
        }
    }

    public function deleteModelByInfoblockId($id)
    {
        if (empty($id)) return false;

        $infoblock = InfoblockModel::getInfoblockById($id);

        if (!empty($infoblock) && !empty($infoblock['CODE'])) {

            $symbolCode = $infoblock['CODE'];

            $generator = $this->generator;
            $prototype = $this->generator->getPrototype();
            $prototype->setSymbolCode($symbolCode);
            if ($prototype->setClassNameBySymbolCode()) {
                $fileName = $prototype->getClass();
                $fileNameExt = $fileName . $generator->getExt();
                $filePath = LetsCore::getEnv(LetsCore::GENERATE_INFOBLOCK_MODELS_PATH)
                    . $fileNameExt;

                $generatedFilePath = LetsCore::getEnv(LetsCore::GENERATE_INFOBLOCK_MODELS_PATH)
                    . LetsCore::getEnv(LetsCore::GENERATE_INFOBLOCK_GENERATED_MODELS_PATH)
                    . $fileNameExt;

                $files = $generator->files;

                if ($files->exists($filePath) && $files->exists($generatedFilePath)) {
                    $files->delete([$filePath, $generatedFilePath]);
                    return true;
                }
            }
        }

        return false;
    }

    private function initPrototype($params)
    {
        if (empty($params)) return false;

        $this->prototype->setSymbolCode($params[static::SYMBOL_CODE]);
        $this->prototype->setClassNameBySymbolCode();
        $this->generator->setCommentIsDemanded(false);
        $this->generator->setGeneratedMode(false);
        $this->prototype->setNamespace($params[static::NAMESPACE]);
        $this->prototype->setParentNamespace($params[static::GENERATED_PARENT_NAMESPACE]);
        $this->generator->setParentClassAlternativeNameDemanded(true);
        $this->generator->setParentClassAlternativeName(static::GENERATED_CLASS_NAME);
        $this->prototype->setParentClass($params[static::GENERATED_PARENT_CLASS_NAME]);
        return true;
    }

    private function initGeneratedPrototype($params)
    {
        if (empty($params)) return false;
        $this->prototype->setSymbolCode($params[static::SYMBOL_CODE]);
        $this->generator->setCommentIsDemanded(true);
        $this->generator->setGeneratedMode(true);
        $this->prototype->setComment($this->prototype->generated());
        $this->prototype->setClassNameBySymbolCode();
        $this->prototype->setNamespace($params[static::GENERATED_NAMESPACE]);
        $this->prototype->setParentNamespace($params[static::PARENT_NAMESPACE]);
        $this->prototype->setParentClass($params[static::PARENT_CLASS_NAME]);
        $this->prototype->setInfoblockId($params[static::IBLOCK_ID]);
        $this->prototype->setBitrixProperties($params[static::PROPERTIES]);
        return true;
    }
}