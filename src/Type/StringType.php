<?php

namespace WindBridges\Schema\Type;


class StringType extends Type
{

    function match(): bool
    {
        return is_string($this->value);
    }

    protected function cast()
    {
        return $this->value === null ? null : (string)$this->value;
    }

}