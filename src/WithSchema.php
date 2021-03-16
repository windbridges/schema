<?php


namespace WindBridges\Schema;


use Exception;
use Symfony\Component\Yaml\Yaml;

trait WithSchema
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
        $schema = Yaml::parseFile(self::getSchemaPath());
        $factory = new ClassFactory(static::class, $schema);
        return $factory->create($data, $displayPath);
    }

    static protected function getSchemaPath(): string
    {
        $class = static::class;

        if (!property_exists($class, 'schemaPath')) {
            throw new Exception("Schema path must be defined in $class::\$schemaPath");
        }

        return $class::$schemaPath;
    }
}