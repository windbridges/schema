<?php

namespace WindBridges\Schema\Properties;


use WindBridges\Schema\WithAnnotationValidator;

class PropertyDefinitionData
{
    use WithAnnotationValidator;

    /**
     * @var mixed
     * @type string|array
     * @required
     */
    public $type;

    /**
     * @var bool
     * @type boolean
     */
    public $required;

    /**
     * @var mixed
     * @type string|number|boolean|enum
     * @noinspection PhpUndefinedClassInspection
     */
    public $default;

    /**
     * @var PropertyDefinitionData[]
     * @type object
     * @items WindBridges\Schema\Properties\PropertyDefinitionData
     */
    public $props;

    /**
     * @var string
     * @type string
     */
    public $class;

    /**
     * @var array
     * @type array
     */
    public $items;
}