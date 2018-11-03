<?php


namespace Budry\XML;


class Validator
{
    public static function validate($fileName, $schema)
    {
        $reader = new \XMLReader();
        if (!$reader->open($fileName)) {
            throw new \Exception("File '{$fileName}' can't be opened");
        }
        if (!$reader->setSchema($schema)) {
            throw new \Exception("Schema '{$fileName}' can't be opened");
        }
        while ($reader->read()) {
        };
    }
}