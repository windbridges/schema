<?php

namespace WindBridges\Schema\Properties;


use Exception;

class PropertyDefinition
{
    /** @var PropertyDefinitionData */
    protected $definitionData;

    protected $name;

    /**
     * @param string $name
     * @param array|PropertyDefinitionData $schemaOrDefinitionData
     * @throws Exception
     */
    public function __construct(string $name, $schemaOrDefinitionData)
    {
        $this->name = $name;

        if (is_array($schemaOrDefinitionData)) {
            $schemaOrDefinitionData = PropertyDefinitionData::create($schemaOrDefinitionData);
        }

        $this->definitionData = $schemaOrDefinitionData;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string|array
     */
    public function getType()
    {
        return $this->definitionData->type;
    }

    public function isRequired(): bool
    {
        return $this->definitionData->required;
    }

    public function getDefault()
    {
        return $this->definitionData->default;
    }

    /**
     * @return array|null
     */
    public function getItems(): ?array
    {
        return $this->definitionData->items;
    }

    public function toSchema(): array
    {
        return $this->definitionData->toSchema();
    }
}