<?php

namespace WindBridges\Schema\Properties;


use ArrayIterator;
use IteratorAggregate;
use Webmozart\Assert\Assert;
use WindBridges\Schema\CollectionTrait;

class PropertyDefinitionContainer implements IteratorAggregate
{
    use CollectionTrait;

    protected $props = [];

    public function add(PropertyDefinition $property)
    {
        Assert::keyNotExists($this->props, $property->getName());

        $this->props[$property->getName()] = $property;
    }

    public function create(string $name, $type, ?bool $required, $default, ?array $items)
    {
        $prop = new PropertyDefinition($name, $type, $required, $default, $items);
        $this->add($prop);
    }

    public function has(string $name): bool
    {
        return array_key_exists($name, $this->props);
    }

    public function getNameList()
    {
        return array_keys($this->props);
    }

    public function getIterator()
    {
        return new ArrayIterator($this->props);
    }

    public function getObjectSchema()
    {
        $schema = [
            'type' => 'object',
            'props' => []
        ];

        $this->each(function (PropertyDefinition $def) use(&$schema) {
            $schema['props'][$def->getName()] = $def->toSchema();
        });

        return $schema;
    }

    public function getArraySchema()
    {
        $schema = [
            'type' => 'array',
            'items' => [
                'type' => 'object',
                'props' => []
            ]
        ];

        $this->each(function (PropertyDefinition $def) use(&$schema) {
            $schema['items']['props'][$def->getName()] = $def->toSchema();
        });

        return $schema;
    }
}