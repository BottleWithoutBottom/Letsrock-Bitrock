<?php

namespace Bitrock\Utils\FileGenerator\Prototypes;
use Bitrock\Utils\FileGenerator\Prototypes\CommentTrait;

class ClassPrototype extends AbstractPrototype
{
    use CommentTrait;
    protected string $namespace = '';
    protected string $class = '';
    protected string $parentClass = '';
    protected string $parentNamespace = '';
    protected string $comment = '';
    protected array $properties;

    /**
     * @return string
     */
    public function getClass()
    {
        return isset($this->class) ? $this->class : '';
    }

    /**
     * @param string $class
     */
    public function setClass($class)
    {
        if (empty($class)) return false;

        $this->class = $class;
        return true;
    }

    /**
     * @return array
     */
    public function getProperties() {
        return $this->properties;
    }

    /**
     * @param array $properties
     */
    public function setProperties($properties): void
    {
        $this->properties = $properties;
    }

    /**
     * @return mixed
     */
    public function getNamespace()
    {
        return isset($this->namespace) ? $this->namespace : '';
    }

    /**
     * @param mixed $namespace
     */
    public function setNamespace($namespace): void
    {
        $this->namespace = $namespace;
    }

    public function getParentNamespace()
    {
        return isset($this->parentNamespace) ? $this->parentNamespace : '';
    }

    public function setParentNamespace($namespace)
    {
        $this->parentNamespace = $namespace;
    }

    public function getParentClass()
    {
        return isset($this->parentClass) ? $this->parentClass : '';
    }

    public function setParentClass($class)
    {
        $this->parentClass = $class;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function setComment($comment): bool
    {
        if (empty($comment)) return false;

        $this->comment = $comment;
        return true;
    }
}