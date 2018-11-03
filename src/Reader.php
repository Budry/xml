<?php

namespace Budry\XML;

use Budry\XML\Entity\Entity;

class Reader
{
    private $reader;

    /**
     * Reader constructor.
     * @param string $fileName
     * @throws \Exception
     */
    public function __construct($fileName)
    {
        $this->reader = new \XMLReader();
        if (!$this->reader->open($fileName)) {
            throw new \Exception("Cannot open file '{$fileName}'");
        }
    }

    /**
     * @param Entity $entity
     * @return Entity|null
     */
    public function read(Entity $entity)
    {
        while ($this->reader->read()) {
            $name = $entity::getNamespace() . ':' . $entity::getTagName();
            if ($this->reader->name === $name && $this->reader->nodeType === \XMLReader::ELEMENT) {
                return $entity->deserialize(new \SimpleXMLElement($this->reader->readOuterXml()));
            }
        }
        return null;
    }
}