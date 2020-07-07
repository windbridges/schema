<?php

namespace WindBridges\Schema\Properties;


use WindBridges\Schema\WithAnnotationValidator;

class PropertyDefinitionData
{
    use WithAnnotationValidator;

    /**
     * @var string|array
     * @type string|array
     * @required
     */
    public $type;

    /**
     * @var bool
     * @type boolean
     */
    public $required = false;

    /**
     * @var string|int|float|bool|null|array
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
     * @var string|null
     * @type string
     */
    public $class;

    /**
     * @var array|null
     * @type array
     */
    public $items;

    public function toSchema()
    {
        $result = [
            'type' => $this->type,
        ];

        if ($this->required) {
            $result['required'] = true;
        }

        if ($this->default !== null) {
            $result['default'] = $this->default;
        }

        if ($this->class) {
            $result['class'] = $this->class;
        }

        if ($this->props) {
            /**
             * @var string $name
             * @var self $prop
             */
            foreach ($this->props as $name => $prop) {
                $result['props'][$name] = $prop->toSchema();
            }
        }

        if ($this->items) {
            $result['items'] = $this->items;
        }

        return $result;
    }
}