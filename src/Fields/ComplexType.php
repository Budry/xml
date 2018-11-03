<?php

namespace Budry\XML\Fields;

use Budry\XML\Entity\Entity;

class ComplexType extends ElementType
{
    /** @var null|string */
    public $class;

    /**
     * ComplexType constructor.
     * @param string $class
     * @param string $type
     */
    public function __construct($class, $type = ElementType::SINGLE_TYPE)
    {
        $this->class = $class;
        parent::__construct($class::getTagName(), $class::getNamespace(), $type);
    }

    /**
     * @return null|string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return Entity
     */
    public function getInstance()
    {
        return new $this->class;
    }
}