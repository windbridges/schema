<?php

namespace WindBridges\Schema\Type;


class ObjectType extends Type
{
    function match(): bool
    {
        return is_array($this->value);
    }

    protected function cast()
    {
        $displayPath = $this->path ?: 'root';
//        $aliases = [];
        $value = null;

//        if (isset($this->schema['aliases'])) {
//            $aliases = $this->schema['aliases'];
//
//            if (!is_array($aliases)) {
//                throw new TypeException("Aliases must be an array at '{$displayPath}'");
//            }
//        }

        if (isset($this->schema['props'])) {
            $props = $this->schema['props'];

            if (!is_array($props)) {
                throw new SchemaDefinitionException("Properties in '{$displayPath}' should be an array");
            }

            if ($this->value !== null) {
                $value = [];

                foreach ($props as $name => $options) {
//                    $alias = array_search($name, $aliases) ?: $name;
//                    $alias = $aliases[$name] ?? $name;

                    if (empty($options['required']) && !empty($this->schema['skip_empty']) && !isset($this->value[$name])) {
                        continue;
                    }

                    if (isset($value[$name])) {
                        throw new SchemaDefinitionException("Overwriting existing property '{$displayPath}.{$name}'");
                    }

                    $nodePath = $this->path ? $this->path . '.' . $name : $name;
                    $nodeValue = $this->value[$name] ?? null;
                    $value[$name] = $this->model->createTypeObject($options, $nodeValue, $nodePath)->getValue();
                }

                $diff = array_diff(array_keys($this->value), array_keys($props));

                if ($diff) {
                    $unknown = join(', ', $this->enquoteArrayStrings($diff));
                    $available = join(', ', $this->enquoteArrayStrings(array_keys($props)));
                    $txtUnknown = count($diff) > 1 ? 'properties' : 'property';
                    $txtAvail = count($props) > 1 ? 'properties' : 'property';
                    throw new ValidationException("Unknown {$txtUnknown} {$unknown} specified for \"{$displayPath}\". Available {$txtAvail} {$available}.");
                }
            }
        } elseif (isset($this->schema['class'])) {
            # class transformer will do the rest by itself
            # just pass the value back as is
            $value = $this->value;
        }

        # class initialization is moved to Type::validate()

        if (!isset($this->schema['props']) && !isset($this->schema['class']) && $this->value !== null) {
            $itemsDef = $this->schema['items'] ?? null;
            $value = [];

            if ($itemsDef && !is_array($itemsDef)) {
                throw new SchemaDefinitionException("Value for '{$displayPath}.items' should be an array");
            }

            foreach ($this->value as $n => $v) {
                if (is_int($n)) {
                    throw new ValidationException("Object '{$displayPath}' must contain property keys, otherwise use array");
                }

//                $alias = $aliases[$n] ?? $n;

                if ($itemsDef) {
                    $value[$n] = $this->model->createTypeObject($itemsDef, $v, "{$this->path}[$n]")->getValue();
                } else {
                    $value[$n] = $v;
//                    $value[$alias] = $v;
                }
            }
        }

        return $value;
    }

    private function enquoteArrayStrings(array $strings, $quote = '"')
    {
        return array_map(function ($str) use($quote) {
            return "{$quote}{$str}{$quote}";
        }, $strings);
    }
}