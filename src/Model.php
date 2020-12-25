<?php

namespace WindBridges\Schema;

use WindBridges\Schema\Type\ArrayType;
use WindBridges\Schema\Type\BooleanType;
use WindBridges\Schema\Type\ClassType;
use WindBridges\Schema\Type\EnumType;
use WindBridges\Schema\Type\NumberType;
use WindBridges\Schema\Type\ObjectType;
use WindBridges\Schema\Type\SchemaDefinitionException;
use WindBridges\Schema\Type\StringType;
use WindBridges\Schema\Type\Type;
use WindBridges\Schema\Type\ValidationException;

class Model
{
    static protected $types = [
        'string' => StringType::class,
        'number' => NumberType::class,
        'boolean' => BooleanType::class,
        'object' => ObjectType::class,
        'array' => ArrayType::class,
        'enum' => EnumType::class,
        'class' => ClassType::class
    ];

    protected $schema = [];
    protected $classes = [];

    public function __construct($schema)
    {
        $this->schema = $schema;

        // $this->registerType('string', StringType::class);
        // $this->registerType('number', NumberType::class);
        // $this->registerType('boolean', BooleanType::class);
        // $this->registerType('object', ObjectType::class);
        // $this->registerType('array', ArrayType::class);
        // $this->registerType('enum', EnumType::class);
    }

    public function create($data, $path = null)
    {
        $typeObj = $this->createTypeObject($this->schema, $data, $path);

        return $typeObj->getValue();
    }

    static public function registerType($name, $class)
    {
        self::$types[$name] = $class;
        // $this->types[$name] = $class;
    }

    /**
     * @param string $alias
     * @param callable|string $classOrHandler Class name or init handler
     * @throws SchemaDefinitionException
     */
    public function registerClass($alias, $classOrHandler)
    {
        if (is_string($classOrHandler) || is_callable($classOrHandler)) {
            $this->classes[$alias] = $classOrHandler;
        } else {
            throw new SchemaDefinitionException('Unable to register class. You should provide class name or callable handler');
        }
    }

    public function getClass($alias)
    {
        # check if alias actually is a full class name
        if (class_exists($alias)) {
            return $alias;
        }

        if (!isset($this->classes[$alias])) {
            throw new SchemaDefinitionException("Class alias '{$alias}' is not registered");
        }

        return $this->classes[$alias];
    }

    public function createTypeObject($schema, $data, $path = null)
    {
        $value = null;
        $displayPath = $path ? $path : 'root';

        if (is_string($schema)) {
            $parser = new InlineDefinitionParser($schema);
            $schema = $parser->getDefinitionData()->toSchema();
        }

        if (!is_array($schema)) {
            throw new SchemaDefinitionException("Schema format is invalid: {$displayPath}");
        }

        if (!isset($schema['type'])) {
            throw new SchemaDefinitionException("Schema has no type defined: {$displayPath}");
        }

        $types = $schema['type'];

        if (!is_array($types)) {
            $types = [$types];
        }

        foreach ($types as $type) {
            if (!isset(self::$types[$type])) {
                throw new SchemaDefinitionException("Type '{$type}' is not registered (used in '{$displayPath}')");
            }
        }

        $typeObj = $this->getTypeObject($types, $data, $schema, $path);

        return $typeObj;
    }

    public function getSchema()
    {
        return $this->schema;
    }

    protected function getTypeObject(array $types, $value, $schema, $path)
    {
        $displayPath = $path ? $path : 'root';

//        if (in_array('array', $types) && in_array('object', $types)) {
//            throw new TypeException("Property '{$path}' accepts types [array, object]. "
//                . "Only one of those types allowed at the same time");
//        }

        foreach ($types as $type) {
            $class = self::$types[$type];

            /** @var Type $typeObj */
            $typeObj = new $class($this, $value, $schema, $path);

            if (is_null($value) || $typeObj->match()) {
                $typeObj->validate();
                return $typeObj;
            }
        }

        $typeList = array_map(function ($typeName) {
            return "'{$typeName}'";
        }, $types);

        $typeList = join(', ', $typeList);
        $currentType = gettype($value);
        throw new ValidationException("'{$displayPath}' requires value of type {$typeList}, '$currentType' given");
    }

}