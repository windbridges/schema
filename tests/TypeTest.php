<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use WindBridges\Schema\ModelObject;

class TypeTest extends TestCase
{

    function testInlineDefinition()
    {
        $n = 10;
        $v = ModelObject::validate('number', $n);
        $this->assertEquals($v, $n);
    }

    function testInlineDefinitionOptionalValue()
    {
        $n = null;
        $v = ModelObject::validate('number', $n);
        $this->assertEquals($v, $n);
    }

    function testInlineDefinitionRequiredValue()
    {
        $n = null;
        $v = ModelObject::validate('number*', $n);
        $this->assertEquals($v, $n);
    }

    function testInlineDefinitionDefaultValue()
    {
        $n = null;
        $v = ModelObject::validate('number:10', $n);
        $this->assertEquals(10, $v);
    }

    function testProperType()
    {
        $str = 'This is string';

        $v = ModelObject::validate([
            'type' => 'string'
        ], $str);

        $this->assertEquals($str, $v);
    }

    function testWrongType()
    {
        $this->expectExceptionMessage("'root' requires value of type 'number', 'string' given");

        ModelObject::validate([
            'type' => 'number'
        ], 'This is string');
    }

    function testStringToNumberCasting()
    {
        $numStr = '1';

        $v = ModelObject::validate([
            'type' => 'number'
        ], $numStr);

        $this->assertEquals($numStr, $v);
    }

    function testStringToBoolCasting()
    {
        $str = '1';

        $v = ModelObject::validate([
            'type' => 'boolean'
        ], $str);

        $this->assertEquals($str, $v);
    }
}
