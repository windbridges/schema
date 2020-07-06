<?php

namespace WindBridges\Schema;


use WindBridges\Schema\Type\SchemaDefinitionException;
use Symfony\Component\Yaml\Yaml;

class ModelObject
{
    protected $schema;
    protected $path;
    protected $model;

    static public function create(array $schema)
    {
        return new self($schema);
    }

    static public function validate(array $schema, $data, string $displayPath = null)
    {
        return self::create($schema)->getModel()->create($data, $displayPath);
    }

    public function __construct(array $schema)
    {
        $this->schema = $schema;
        $this->model = new Model($schema);
    }

    public function createFromArray(array $data, $path = null)
    {
        return $this->model->create($data, $path);
    }

    public function createFromFile($filePath, $rootNode = null, $dataPath = null)
    {
        $data = Yaml::parseFile($filePath);

        if ($rootNode) {
            if (!isset($data[$rootNode])) {
                throw new SchemaDefinitionException("Schema must have a root node with key '{$rootNode}'");
            }

            $data = [$rootNode];
        }

        return $this->createFromArray($data, $dataPath);
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    public function registerClass($alias, $classOrHandler)
    {
        $this->model->registerClass($alias, $classOrHandler);
        return $this;
    }

    public function registerType($name, $class)
    {
        $this->model->registerType($name, $class);
        return $this;
    }
}