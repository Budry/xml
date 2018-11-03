<?php

namespace Budry\XML\Fields;

abstract class ElementType
{
    const SINGLE_TYPE = 'single';
    const ARRAY_TYPE = 'array';

    /** @var null|string */
    private $namespace;

    /** @var string */
    private $name;

    /** @var string */
    private $type;

    /**
     * ElementType constructor.
     * @param string $name
     * @param string|null $namespace
     * @param string $type
     */
    public function __construct($name, $namespace = null, $type = self::SINGLE_TYPE)
    {
        $this->name = $name;
        $this->namespace = $namespace;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @param mixed $value
     * @return string
     */
    public function serialize($value)
    {
        return (string)$value;
    }

    /**
     * @param \SimpleXMLElement $element
     * @return string
     */
    public function deserialize(\SimpleXMLElement $element)
    {
        return $element->__toString();
    }
}