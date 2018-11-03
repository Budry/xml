<?php

namespace Budry\XML\Attributes;

class Attribute
{
    /** @var string */
    private $name;

    /**
     * Attribute constructor.
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $value
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