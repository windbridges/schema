<?php

namespace WindBridges\Schema;


use WindBridges\Schema\Properties\PropertyDefinitionData;


/**
 * Allows to specify schema in short single-line way.
 * Examples:
 * "string"
 * "string*" - Required
 * "string:Default value" - With default value
 *
 * @package WindBridges\Schema
 */
class InlineDefinitionParser
{
    /** @var PropertyDefinitionData */
    protected $definitionData;

    public function __construct(string $def)
    {
        $this->definitionData = $this->parse($def);
    }

    public function getDefinitionData(): PropertyDefinitionData
    {
        return $this->definitionData;
    }

    protected function parse(string $def): PropertyDefinitionData
    {
        $result = new PropertyDefinitionData();
        $def = trim($def);

        if (substr($def, -1) == '*') {
            $result->required = true;
            $def = substr($def, 0, -1);
        }

        if (strpos($def, ':') !== false) {
            [$type, $default] = explode(':', $def, 2);
            $def = $type;
            $result->default = $default;
        }

        $result->type = $def;

        return $result;
    }
}