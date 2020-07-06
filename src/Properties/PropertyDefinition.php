<?php

namespace WindBridges\Schema\Properties;


use WindBridges\Schema\InvalidSchemaException;

class PropertyDefinition
{
    protected $name;
    protected $type = 'string';
    protected $required = false;
    protected $default;
    protected $items;

    /**
     * @param string $name
     * @param string|array $type
     * @param bool $required
     * @param mixed $default
     * @param array|null $items
     * @throws InvalidSchemaException
     */
    public function __construct(string $name, $type, ?bool $required, $default, ?array $items)
    {
        $this->name = $name;
        $this->type = $type;
        $this->required = (bool)$required;
        $this->default = $default;
        $this->items = $items;

        if ($default !== null && $required) {
            throw new InvalidSchemaException("Property '{$name}' cannot have a default value since it is required");
        }
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string|array
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * @return mixed|null
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @return array|null
     */
    public function getItems(): ?array
    {
        return $this->items;
    }



    public function toSchema()
    {
        return [
            'type' => $this->type,
            'required' => $this->required,
            'default' => $this->default,
            'items' => $this->items
        ];
    }
}