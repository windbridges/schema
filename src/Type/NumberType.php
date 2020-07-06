<?php

namespace WindBridges\Schema\Type;

use RangeException;

class NumberType extends Type
{
    function match(): bool
    {
        return is_numeric($this->value);
    }

    protected function cast()
    {
        if ($this->value === null) {
            return null;
        } else {
            $displayPath = $this->path ?: 'root';

            if (!empty($this->schema['min']) && $this->value < $this->schema['min']) {
                throw new RangeException("'{$displayPath}' can not be smaller than {$this->schema['min']}");
            }

            if (!empty($this->schema['max']) && $this->value > $this->schema['max']) {
                throw new RangeException("'{$displayPath}' can not be greater than {$this->schema['max']}");
            }

            return strpos($this->value, '.') !== false ? (double)$this->value : (int)$this->value;
        }
    }
}