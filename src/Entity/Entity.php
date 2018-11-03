<?php

namespace Budry\XML\Entity;

use Budry\XML\Fields\ComplexType;
use Budry\XML\Fields\ElementType;
use Budry\XML\Fields\SimpleType;

abstract class Entity implements IEntity
{
    /** @var array */
    private $attributes;

    /** @var array */
    private $values;

    /**
     * Entity constructor.
     * @param array $values
     * @param array $attributes
     */
    public function __construct(array $values = [], array $attributes = [])
    {
        $this->values = $values;
        $this->attributes = $attributes;
    }

    /**
     * @param \XMLWriter $writer
     */
    public function serialize(\XMLWriter $writer)
    {
        $this->serializeStart($writer);
        foreach ($this->values as $name => $value) {
            if (is_array($value) && isset($value[0]) && $value[0] instanceof Entity) {
                $name = $value[0]::getTagName();
            }
            if ($value instanceof Entity) {
                $name = $value::getTagName();
            }
            $field = $this->getFieldDefinition($name);
            if ($field) {
                if ($field->getType() === ElementType::ARRAY_TYPE && is_array($value)) {
                    foreach ($value as $item) {
                        $this->serializeElement($writer, $field, $item);
                    }
                } else if ($field->getType() === ElementType::SINGLE_TYPE) {
                    $this->serializeElement($writer, $field, $value);
                }
            }
        }
        $this->serializeEnd($writer);
    }

    /**
     * @param \XMLWriter $writer
     */
    public function serializeStart(\XMLWriter $writer)
    {
        if ($this::getNamespace()) {
            $writer->startElementNs($this::getNamespace(), $this::getTagName(), null);
        } else {
            $writer->startElement($this::getTagName());
        }
        foreach ($this::getNamespaces() as $name => $uri) {
            $writer->writeAttributeNs('xmlns', $name, null, $uri);
        }
        foreach ($this->attributes as $name => $value) {
            $attributeDefinition = $this->getAttributeDefinition($name);
            $writer->writeAttribute($name, $attributeDefinition->serialize($value));
        }
    }

    /**
     * @return array
     */
    public static function getNamespaces()
    {
        return [];
    }

    /**
     * @param string $name
     * @return \Budry\XML\Attributes\Attribute|null
     */
    private function getAttributeDefinition($name)
    {
        foreach ($this::getAttributesDefinition() as $item) {
            if ($item->getName() === $name) {
                return $item;
            }
        }

        return null;
    }

    /**
     * @return \Budry\XML\Attributes\Attribute[]
     */
    public static function getAttributesDefinition()
    {
        return [];
    }

    /**
     * @param string $name
     * @return ComplexType|SimpleType|null
     */
    private function getFieldDefinition($name)
    {
        foreach ($this::getFieldsDefinitions() as $field) {
            if ($field->getName() === $name) {
                return $field;
            }
        }

        return null;
    }

    /**
     * @param \XMLWriter $writer
     * @param ElementType $field
     * @param $value
     */
    private function serializeElement(\XMLWriter $writer, ElementType $field, $value)
    {
        if ($field instanceof SimpleType) {
            $writer->startElement($field->getName());
            $writer->text($field->serialize($value));
            $writer->endElement();
        } else if ($field instanceof ComplexType && $value instanceof Entity) {
            $value->serialize($writer);
        }
    }

    /**
     * @param \XMLWriter $writer
     */
    public function serializeEnd(\XMLWriter $writer)
    {
        $writer->endElement();
    }

    /**
     * @param \SimpleXMLElement $element
     * @return $this
     */
    public function deserialize(\SimpleXMLElement $element)
    {
        foreach ($element->attributes() as $name => $attribute) {
            $attributeDefinition = $this->getAttributeDefinition($name);
            if ($attributeDefinition) {
                $this->attributes[$name] = $attributeDefinition->deserialize($attribute);
            }
        }

        $namespaces = $element->getNamespaces(true);
        foreach ($namespaces as $prefix => $uri) {
            foreach ($element->children($prefix, true) as $child) {
                $this->deserializeElement($child);
            }
        }

        foreach ($element->children() as $child) {
            $this->deserializeElement($child);
        }

        return $this;
    }

    /**
     * @param \SimpleXMLElement $child
     */
    private function deserializeElement(\SimpleXMLElement $child)
    {
        $fieldDefinition = $this->getFieldDefinition($child->getName());
        if ($fieldDefinition) {
            if ($fieldDefinition instanceof SimpleType) {
                if ($fieldDefinition->getType() === ElementType::SINGLE_TYPE) {
                    $this->setValue($child->getName(), $fieldDefinition->deserialize($child));
                } else if ($fieldDefinition->getType() === ElementType::ARRAY_TYPE) {
                    $this->addValue($child->getName(), $fieldDefinition->deserialize($child));
                }
            } else if ($fieldDefinition instanceof ComplexType) {
                $class = $fieldDefinition->getClass();
                /** @var Entity $instance */
                $instance = new $class;
                if ($fieldDefinition->getType() === ElementType::SINGLE_TYPE) {
                    $this->setValue($child->getName(), $instance->deserialize($child));
                } else if ($fieldDefinition->getType() === ElementType::ARRAY_TYPE) {
                    $this->addValue($child->getName(), $instance->deserialize($child));
                }
            }
        }
    }

    /*
     *
     * DEFAULT VALUES
     *
     */

    /**
     * @param string $name
     * @param string $value
     */
    public function setValue($name, $value)
    {
        $this->values[$name] = $value;
    }

    /**
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function addValue($name, $value)
    {
        if (!isset($this->values[$name])) {
            $this->values[$name] = [];
        }
        $this->values[$name][] = $value;

        return $this;
    }
}