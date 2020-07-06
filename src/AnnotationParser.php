<?php

namespace WindBridges\Schema;


use WindBridges\Schema\Type\SchemaDefinitionException;
use Exception;
use ReflectionClass;
use ReflectionObject;

class AnnotationParser
{
    /** @var array */
    protected $schema;

    public function __construct($classOrObject)
    {
        if (is_string($classOrObject)) {
            $reflection = new ReflectionClass($classOrObject);
        } elseif (is_object($classOrObject)) {
            $reflection = new ReflectionObject($classOrObject);
        } else {
            throw new Exception("Wrong argument passed to AnnotationParser");
        }

        $this->parse($reflection);
    }

    public function getSchema()
    {
        return $this->schema;
    }

    protected function parse(ReflectionClass $reflection)
    {
        $this->schema = [
            'type' => 'object',
            'props' => []
        ];

        $props = $reflection->getProperties();

        foreach ($props as $prop) {
            $comment = $prop->getDocComment();
            $schema = $this->parseDocBlock($comment);

            if ($schema) {
                # save properties with @type attribute only
                # properties without @type attr are not affected by validator
                $this->schema['props'][$prop->getName()] = $schema;
            }
        }
    }

    protected function parseDocBlock($block)
    {
        preg_match_all('/^\s*\*?\s*@([-a-z0-9_]+)\s*([^\r\n]*)[\r\n]/im', $block, $m, PREG_SET_ORDER);

        if (!$m) {
            return [];
        }

        $result = [];

        foreach ($m as $item) {
            $name = $item[1];
            $value = $item[2];

            if (empty($name)) {
                throw new SchemaDefinitionException('Wrong schema argument: ' . $name);
            }

            if ($value === '') {
                $value = true;
            }

            $value = explode('|', $value);
            $value = array_map('trim', $value);

            if (count($value) == 1) {
                $value = $value[0];
            }

            $result[$name] = $value;
        }

        $result = $this->postProcessBlock($result);

        return $result;
    }

    private function postProcessBlock(array $block)
    {
        switch ($block['type'] ?? null) {
            case 'array':
                if ($block['default'] ?? null) {
                    $block['default'] = \GuzzleHttp\json_decode($block['default']);
                }

                break;

            case 'object':
                # 'items' in annotations treated as class names
                if ($block['items'] ?? null) {
                    $block['items'] = [
                        'type' => 'object',
                        'class' => $block['items']
                    ];
                }

                if ($block['default'] ?? null) {
                    $block['default'] = \GuzzleHttp\json_decode($block['default']);
                }

                break;

            case null:
                $block = null;
                break;
        }

        return $block;
    }
}