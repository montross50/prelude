<?php

namespace Prelude;

class BooleansTest extends \PHPUnit_Framework_TestCase {

    function truthyValues() {
        return array(
            array(true),
            array(1),
            array(1.23),
            array(array(1)),
            array(array(array())),
            array($this),
        );
    }

    function falsyValues() {
        return array(
            array(null),
            array(false),
            array(0),
            array(0.0),
            array(array()),
        );
    }

    function truthyStrings() {
        return array(
            array('yes'),
            array('no'),
            array('true'),
            array('test'),
            array('some false value'),
        );
    }

    function falsyStrings() {
        return array(
            array(''),
            array('0'),
            array('false'),
            array('  false  '),
        );
    }

    function truthy() {
        return array_merge($this->truthyValues(), $this->truthyStrings());
    }

    /**
     * @dataProvider truthy
     */
    function testParse_withTrue($value) {
        $this->assertTrue(Booleans::parse($value));
    }

    /**
     * @dataProvider falsyValues
     */
    function testParse_withFalse($value) {
        $this->assertFalse(Booleans::parse($value));
    }

    /**
     * @dataProvider truthyValues
     */
    function testToString_withTrue($value) {
        $this->assertEquals('true', Booleans::toString($value));
    }

    /**
     * @dataProvider falsyValues
     */
    function testToString_withFalse($value) {
        $this->assertEquals('false', Booleans::toString($value));
    }
}
