<?php

namespace WindBridges\Schema\Tests;

use PHPUnit\Framework\TestCase;
use WindBridges\Schema\ModelObject;

class TypeTest extends TestCase
{
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
