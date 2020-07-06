<?php

namespace WindBridges\Schema\Type;


class EnumType extends Type
{
    function match(): bool
    {
        return is_string($this->value);
    }

    public function getValue()
    {
        $default = $this->schema['default'] ?? null;

        if ($default !== null && !in_array($default, $this->schema['items'])) {
            throw new ValidationException('Unable to set default value, because it is not allowed');
        }

        return $this->castedValue === null ? $default : $this->castedValue;
    }

    protected function cast()
    {
        $displayPath = $this->path ?: 'root';

        if (!isset($this->schema['items'])) {
            throw new SchemaDefinitionException('Enum schema should have \'items\' property containing array of string values');
        }

        $this->schema['items'] = (array)$this->schema['items'];

        $value = $this->value === null ? null : (string)$this->value;

        if ($value !== null && !in_array($value, $this->schema['items'])) {
            throw new ValidationException("Value '{$value}' is not allowed in '{$displayPath}'");
        }

        return $value;
    }
}