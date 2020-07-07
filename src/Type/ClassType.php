<?php

namespace WindBridges\Schema\Type;


use WindBridges\Schema\Model;

class ClassType extends Type
{

    public function __construct(Model $loader, $value, $schema, $path)
    {
        parent::__construct($loader, $value, $schema, $path);

        if (empty($schema['class'])) {
            throw new SchemaDefinitionException('When property type is set to "class" then attribute "class" must be present');
        }
    }

    function match(): bool
    {
        $class = (array)$this->schema['class'];

        foreach ($class as $_class) {
            if ($this->value instanceof $_class) {
                return true;
            }
        }

        return false;
    }

    protected function cast()
    {
        return $this->value;
    }
}