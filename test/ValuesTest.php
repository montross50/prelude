<?php

namespace Prelude;

class ValuesTest extends \PHPUnit_Framework_TestCase {

    function values() {
        return array(
            array('null', null),
            array('boolean', true),
            array('boolean', false),
            array('integer', 0),
            array('integer', 1),
            array('double', 0.0),
            array('double', 1.2),
            array('string', '1.2'),
            array('array', array()),
            array('array', array(1)),
            array('array', array(1, 2)),
            array('resource', fopen(__FILE__, 'r')),
            array('object' , $this),
        );
    }

    /**
     * @dataProvider values
     */
    function testTypeOf($type, $value) {
        $this->assertEquals($type, Values::typeOf($value));
    }


    /**
     * @dataProvider values
     */
    function testHashOf($type, $value) {
        if (is_resource($value)) {
            $this->assertEquals('resource', $type);
        } else {
            $this->assertInternalType('string', Values::hashOf($value));
        }
    }
}
