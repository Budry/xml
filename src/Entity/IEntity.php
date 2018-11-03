<?php

namespace Budry\XML\Entity;

use Budry\XML\Attributes\Attribute;
use Budry\XML\Fields\ComplexType;
use Budry\XML\Fields\SimpleType;

interface IEntity
{
    /**
     * @return string
     */
    public static function getTagName();

    /**
     * @return string
     */
    public static function getNamespace();

    /**
     * @return array
     */
    public static function getNamespaces();

    /**
     * @return Attribute[]
     */
    public static function getAttributesDefinition();

    /**
     * @return SimpleType[]|ComplexType[]
     */
    public static function getFieldsDefinitions();

    /**
     * @param \XMLWriter $writer
     * @return void
     */
    public function serialize(\XMLWriter $writer);

    /**
     * @param \SimpleXMLElement $element
     * @return $this
     */
    public function deserialize(\SimpleXMLElement $element);
}