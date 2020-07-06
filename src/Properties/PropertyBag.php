<?php

namespace WindBridges\Schema\Properties;


use Webmozart\Assert\Assert;

class PropertyBag
{
    protected $props = [];

    static public function fromArray(array $props)
    {
        $container = new static();
        $container->props = $props;

        return $container;
    }

    public function get($name)
    {
        Assert::keyExists($this->props, $name, "Property '{$name}' does not exist");

        return $this->props[$name];
    }

    public function asArray()
    {
        return $this->props;
    }

    public function has($name)
    {
        return array_key_exists($name, $this->props);
    }
}