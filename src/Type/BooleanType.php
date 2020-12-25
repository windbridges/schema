<?php

namespace WindBridges\Schema\Type;


class BooleanType extends Type
{

    function match(): bool
    {
        return true;
    }

    protected function cast()
    {
        return $this->value === null ? null : (boolean)$this->value;
    }

}