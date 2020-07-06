<?php

namespace WindBridges\Schema;


trait CollectionTrait
{
    abstract public function getIterator();

    public function each(callable $handler)
    {
        foreach ($this as $key => $value) {
            $handler($value, $key);
        }
    }
}