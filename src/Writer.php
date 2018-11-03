<?php

namespace Budry\XML;

use Budry\XML\Entity\Entity;

class Writer
{
    private $writer;

    /**
     * Writer constructor.
     * @param string $file
     * @throws \Exception
     */
    public function __construct($file)
    {
        $this->writer = new \XMLWriter();
        if (!$this->writer->openUri($file)) {
            throw new \Exception("Cannot write into file '{$file}'");
        }
    }

    /**
     * @param string $version
     * @param string $charset
     */
    public function startDocument($version, $charset)
    {
        $this->writer->startDocument($version, $charset);
    }

    /**
     * @param Entity $entity
     */
    public function write(Entity $entity)
    {
        $entity->serialize($this->writer);
        $this->writer->flush();
    }

    /**
     * @param Entity $entity
     */
    public function writeStart(Entity $entity)
    {
        $entity->serializeStart($this->writer);
    }

    /**
     * @param Entity $entity
     */
    public function writeEnd(Entity $entity)
    {
        $entity->serializeEnd($this->writer);
        $this->writer->flush();
    }
}