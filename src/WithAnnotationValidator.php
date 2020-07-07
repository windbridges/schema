<?php

namespace WindBridges\Schema;

use Exception;

trait WithAnnotationValidator
{
    /**
     * Creates and validates new object from data array
     *
     * @param $data
     * @param string|null $displayPath
     * @return static
     * @throws Exception
     */
    static function create(array $data, ?string $displayPath = null)
    {
        $factory = new ClassFactory(static::class);
        return $factory->create($data, $displayPath);
    }
}