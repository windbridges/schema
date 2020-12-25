<?php

namespace WindBridges\Schema\Tests;

use WindBridges\Schema\ModelObject;
use PHPUnit\Framework\TestCase;

class ModelObjectTest extends TestCase
{
    function testProperty()
    {
        $expected = [
            'first_name' => 'Alex'
        ];

        $value = ModelObject::validate([
            'type' => 'object',
            'props' => [
                'first_name' => [
                    'type' => 'string'
                ]
            ]
        ], $expected);

        $this->assertEquals($expected, $value);
    }

    function testSimplifiedDefinition()
    {
        $expected = [
            'first_name' => 'Alex'
        ];

        $value = ModelObject::validate([
            'type' => 'object',
            'props' => [
                'first_name' => 'string'
            ]
        ], $expected);

        $this->assertEquals($expected, $value);
    }

    function testSimplestDefinition()
    {
        $expected = [
            'first_name' => 'Alex'
        ];

        $value = ModelObject::validate([
            'type' => 'object',
            'props' => [
                'first_name'
            ]
        ], $expected);

        $this->assertEquals($expected, $value);
    }

    function testString()
    {
        $expected = 'Alex';

        $value = ModelObject::validate([
            'type' => 'string'
        ], $expected);

        $this->assertEquals($expected, $value);

        $this->expectExceptionMessage("'root' requires value of type 'string', 'integer' given");

        ModelObject::validate([
            'type' => 'string'
        ], 1);
    }

}
