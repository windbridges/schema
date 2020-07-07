<?php

namespace WindBridges\Schema\Type;

use ReflectionProperty;
use Symfony\Component\PropertyAccess\PropertyAccess;
use WindBridges\Schema\Model;

abstract class Type
{
    protected $model;
    protected $value;
    protected $schema;
    protected $path;
    protected $castedValue;
    protected $title;

    abstract function match(): bool;

    abstract protected function cast();

    /**
     * Type constructor.
     *
     * @param Model $loader
     * @param        $value
     * @param        $schema
     * @param        $path
     * @throws SchemaDefinitionException
     */
    public function __construct(Model $loader, $value, $schema, $path)
    {
        $this->model = $loader;
        $this->value = $value;
        $this->schema = $schema;
        $this->path = $path;
        $this->title = $schema['title'] ?? null;

        if ($this->title !== null && !is_string($this->title)) {
            $displayPath = $path ? $path : '[root]';
            throw new SchemaDefinitionException("Wrong title value for '{$displayPath}'");
        }
    }

    public function updateValue($newValue, $newPath = null, $autoValidate = true)
    {
        $this->value = $newValue;

        if (!is_null($newPath)) {
            $this->path = $newPath;
        }

        if ($autoValidate) {
            $this->validate();
        }
    }

    public function validate()
    {
        $required = !empty($this->schema['required']);
        $isNull = $this->value === null;

        if ($required && $isNull) {
            $path = $this->path ? "'{$this->path}' " : '';
            throw new ValidationException("Property {$path}is required");
        }

        if (!$required && $isNull /*&& array_key_exists('default', $this->schema)*/) {
            $this->value = $this->schema['default'] ?? null;

            if (!$this->match() && $this->value !== null) {
                $path = $this->path ? $this->path : 'root';
                throw new SchemaDefinitionException("Default value for '{$path}' does not match the schema type");
            }
        }

        $this->castedValue = $this->cast();

        if ($this->value && $this->castedValue === null) {
            $class = get_called_class();
            throw new ValidationException("Property '{$this->path}' is defined, but becomes NULL when casting to '{$class}'");
        }

        if (isset($this->schema['class']) && $this->schema['type'] != 'class') {
            $this->castedValue = $this->createClassObject($this->schema['class'], function ($object) {
                if ($this->value) {
                    $accessor = PropertyAccess::createPropertyAccessor();

                    foreach ($this->value as $name => $value) {
                        if ($accessor->isWritable($object, $name)) {
                            $accessor->setValue($object, $name, $value);
                        } else {
                            $prop = new ReflectionProperty($object, $name);
                            $prop->setAccessible(true);
                            $prop->setValue($object, $value);
                            $prop->setAccessible(false);
                        }
                    }
                }
            });
        }

        $this->isValid();
    }

    protected function createClassObject($classOrAlias, callable $defaultConstructor)
    {
        if ($this->getValue() === null) {
            // if value is null, then object should not be created
            // user must set default: [] to create object with default props
            return null;
        }

        $classNameOrHandler = $this->model->getClass($classOrAlias);

        if (is_string($classNameOrHandler)) {
            $className = $classNameOrHandler;

            if (!class_exists($className)) {
                throw new SchemaDefinitionException("Registered class '{$className}' is not found");
            }

            $classObject = new $className();
            call_user_func($defaultConstructor, $classObject);
        } elseif (is_callable($classNameOrHandler)) {
            $handler = $classNameOrHandler;
            $value = $this->getValue();
            $classObject = call_user_func($handler, $value, $classOrAlias);
        } else {
            throw new SchemaDefinitionException("Wrong class name or callable handler for alias '{$classOrAlias}'");
        }

        return $classObject;
    }

    public function getValue()
    {
        return $this->castedValue;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    protected function isValid()
    {
        // use to override
    }
}