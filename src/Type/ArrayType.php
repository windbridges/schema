<?php

namespace WindBridges\Schema\Type;


class ArrayType extends Type
{
    function match(): bool
    {
        return is_array($this->value);
    }

    protected function cast()
    {
        $value = $this->value;

        if (isset($this->schema['items'])) {
            $itemsDef = $this->schema['items'];

            $value = [];

            if ($this->value !== null) {
                foreach ($this->value as $key => $item) {
                    $value[$key] = $this->model->createTypeObject($itemsDef, $item, "{$this->path}[$key]")->getValue();
                }
            }
        }

        # class transformer will do the rest by itself
        # just pass the value back as is

        return $value;
    }
}