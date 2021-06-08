<?php

namespace Bitrock\Utils\FileGenerator\Generator;

use Bitrock\Utils\FileGenerator\Prototypes\AbstractPrototype;
use Bitrock\Utils\FileGenerator\Prototypes\ClassPrototype;
use Bitrock\Utils\FileGenerator\Stubs\ClassStub;

class ClassGenerator extends AbstractGenerator
{
    protected $commentDemanded = false;
    protected $parentClassAlternativeNameDemanded = false;
    protected $parentClassAlternativeName = '';

    protected $commentStubs = [
        '{{commentStub}}', '{{ commentStub }}'
    ];

    protected $namespaceStubs = [
        '{{namespace}}', '{{ namespace }}'
    ];

    protected $classStubs = [
        '{{class}}', '{{ class }}'
    ];

    protected $parentClassStubs = [
        '{{parentClass}}', '{{ parentClass }}'
    ];

    protected $parentNamespaceStubs = [
        '{{parentNamespace}}', '{{ parentNamespace }}'
    ];

    public function __construct(
        ClassPrototype $prototype,
        ClassStub $stub
    ) {
        parent::__construct();
        $this->setPrototype($prototype);
        $this->setStub($stub);
    }

    public function generate(): bool
    {
        if (parent::generate()) {
            if ($this->commentDemanded) {
                $this->placeComment($this->getPrototype()->getComment());
            } else {
                $this->removeComment();
            }

            $class = $this->getPrototype()->getClass();
            if (!empty($class)) {
                $this->placeClass($this->getPrototype());
                $this->setFileName($class);

                $namespace = $this->getPrototype()->getNamespace();
                if (!empty($namespace)) {
                    $this->placeNamespace($this->getPrototype());

                    $this->placeParentClass($this->getPrototype());
                    $this->placeParentNamespace($this->getPrototype());
                    return true;
                }
            }
        }

        return false;
    }

    protected function createConst(
        string $name,
        string $value,
        string $access = 'public',
        bool $disablePreString = false,
        bool $disableLastSymbol = false,
        string $preString = "\t"
    ): string
    {
        if (empty($name) || empty($value)) return '';

        $preStringSymbol = !$disablePreString ? $preString : '';
        $breakStringSymbol = !$disableLastSymbol ? "\n" : '';

        return $preStringSymbol . $access . ' CONST ' . $name . ' = ' . "'" . $value . "'" . ';' . $breakStringSymbol;
    }

    protected function placeComment($comment)
    {
        if (!empty($comment)) {
            foreach ($this->commentStubs as $commentStub) {
                if (strrpos($this->getStubString(), $commentStub)) {
                    $newStub = str_replace($commentStub, $comment, $this->getStubString());
                    $this->stubString = $newStub;
                    return true;
                }
            }
        } else {
            $this->removeComment();
        }
    }

    protected function removeComment()
    {
        foreach ($this->commentStubs as $commentStub) {
            if (strrpos($this->getStubString(), $commentStub)) {
                $newStub = str_replace($commentStub, '', $this->getStubString());
                $this->stubString = $newStub;
                return true;
            }
        }
    }

    protected function placeClass(
        AbstractPrototype $prototype
    ) {
        $class = $prototype->getClass();

        if (!empty($class)) {
            foreach ($this->classStubs as $classStub) {
                if (strrpos($this->getStubString(), $classStub)) {
                    $newStub = str_replace($classStub, $class, $this->getStubString());
                    $this->stubString = $newStub;
                    return true;
                }
            }
        }

        return false;
    }

    protected function placeNamespace(
        AbstractPrototype $prototype
    ) {
        $namespace = $prototype->getNamespace();
        if (!empty($namespace)) {
            foreach ($this->namespaceStubs as $namespaceStub) {
                if (strrpos($this->getStubString(), $namespaceStub)) {
                    $newStub = str_replace($namespaceStub, $namespace, $this->getStubString());
                    $this->stubString = $newStub;
                    return true;
                }
            }
        }

        return false;
    }

    protected function placeParentNamespace(
        AbstractPrototype $prototype
    ) {
        $parentNamespace = $prototype->getParentNamespace();

        if (!empty($parentNamespace)) {
            foreach ($this->parentNamespaceStubs as $parentNamespaceStub) {
                if (strrpos($this->getStubString(), $parentNamespaceStub)) {
                    //Здесь добавляется альтернативное имя класса, для того, чтобы избежать конфликта одинаковых названий у классов
                    if ($this->getParentClassAlternativeNameDemanded() && !empty($this->getParentClassAlternativeName())) {
                        $parentNamespace = $this->getParentClassAlternativeNameString($parentNamespace);
                    }
                    $newStub = str_replace($parentNamespaceStub, $parentNamespace, $this->getStubString());
                    $this->stubString = $newStub;
                    return true;
                }
            }
        } else {
            foreach ($this->parentNamespaceStubs as $parentNamespaceStub) {
                $removalStubString = 'use ' . $parentNamespaceStub . ';';
                if (strrpos($this->getStubString(), $removalStubString)) {
                    $newStub = str_replace($removalStubString, '', $this->getStubString());
                    $this->stubString = $newStub;
                    return true;
                }
            }
        }

        return false;
    }

    protected function placeParentClass(
        AbstractPrototype $prototype
    ) {
        $parentClass = $prototype->getParentClass();

        if (!empty($parentClass)) {
            foreach ($this->parentClassStubs as $parentClassStub) {
                if (strrpos($this->getStubString(), $parentClassStub)) {
                    //Здесь добавляется альтернативное имя класса, для того, чтобы избежать конфликта одинаковых названий у классов
                    if ($this->getParentClassAlternativeNameDemanded() && !empty($this->getParentClassAlternativeName())) {
                        $parentClass = $this->getParentClassAlternativeName();
                    }

                    $newStub = str_replace($parentClassStub, $parentClass, $this->getStubString());
                    $this->stubString = $newStub;
                    return true;
                }
            }
        } else {
            foreach ($this->parentClassStubs as $parentClassStub) {
                $removalStubString = ' extends ' . $parentClassStub;
                if (strrpos($this->getStubString(), $removalStubString)) {
                    $newStub = str_replace($removalStubString, '', $this->getStubString());
                    $this->stubString = $newStub;
                    return true;
                }
            }
        }

        return false;
    }

    public function setCommentIsDemanded(bool $demanded = false)
    {
        $this->commentDemanded = $demanded;
    }

    public function setParentClassAlternativeNameDemanded($demanded = false)
    {
        $this->parentClassAlternativeNameDemanded = $demanded;
    }

    public function getParentClassAlternativeNameDemanded()
    {
        return $this->parentClassAlternativeNameDemanded;
    }

    public function getParentClassAlternativeName()
    {
        return $this->parentClassAlternativeName;
    }

    public function setParentClassAlternativeName($name)
    {
        if (empty($name)) return false;

        $this->parentClassAlternativeName = $name;
        return true;
    }

    public function getParentClassAlternativeNameString($string)
    {
        return $string . ' as ' . $this->getParentClassAlternativeName();
    }
}