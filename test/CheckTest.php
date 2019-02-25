<?php

namespace Prelude;

class CheckTest extends \PHPUnit_Framework_TestCase {

    function truthyValues() {
        return array(
            array(true),
            array(1),
            array(1.23),
            array(array(1)),
            array(array(1.23)),
            array('test'),
            array(array('test')),
            array(array('')),
            array(array(array())),
            array($this),
            array(fopen(__FILE__, 'r')),
            array(new \EmptyIterator),
        );
    }

    function falsyValues() {
        return array(
            array(null),
            array(false),
            array(0),
            array(''),
            array(array()),
        );
    }

    /**
     * @dataProvider truthyValues
     */
    function testArgument_withTruthy($value) {
        Check::argument($value);
        $this->assertTrue((boolean) $value);
    }

    /**
     * @dataProvider falsyValues
     * @expectedException InvalidArgumentException
     */
    function testArgument_withFalsy($value) {
        Check::argument($value);
        $this->fail();
    }

    function testArgumentFormatsMessageLazy() {
        try {
            Check::argument(false, "%d%d%d%d%d%d", 1, 2, 3, 4, 5, 6);
            $this->fail();
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals('123456', $e->getMessage());
        }
    }

    /**
     * @dataProvider truthyValues
     */
    function testState_withTruthy($value) {
        Check::state($value);
        $this->assertTrue((boolean) $value);
    }

    /**
     * @dataProvider falsyValues
     * @expectedException DomainException
     */
    function testState_withFalsy($value) {
        Check::state($value);
        $this->fail();
    }

    function testStateFormatsMessageLazy() {
        try {
            Check::state(false, "%d%d%d%d%d%d", 1, 2, 3, 4, 5, 6);
            $this->fail();
        } catch (\DomainException $e) {
            $this->assertEquals('123456', $e->getMessage());
        }
    }

    function nonEmptyValues() {
        return array(
            array(array(1)),
            array(array(false)),
            array(array('')),
            array(array("string")),
            array(new \ArrayIterator(array(1))),
        );
    }

    function emptyValues() {
        return array(
            array(0),
            array('0'),
            array(false),
            array(null),
            array(array()),
            array(new \EmptyIterator()),
            array(new \ArrayIterator(array())),
            array(new \ArrayObject),
        );
    }

    /**
     * @dataProvider nonEmptyValues
     */
    function testNotEmpty_withNonEmpty($value) {
        $this->assertSame($value, Check::notEmpty($value));
    }

    /**
     * @dataProvider emptyValues
     * @expectedException UnexpectedValueException
     */
    function testNotEmpty_withEmpty($value) {
        Check::notEmpty($value);
        $this->fail();
    }

    function testNotEmptyFormatsMessageLazy() {
        try {
            Check::notEmpty(false, "%d%d%d%d%d%d", 1, 2, 3, 4, 5, 6);
        } catch(\UnexpectedValueException $e) {
            $this->assertEquals('123456', $e->getMessage());
        }
    }

    /**
     * @dataProvider truthyValues
     */
    function testNotNull_withNonNull($value) {
        $this->assertSame($value, Check::notNull($value));
    }

    /**
     * @expectedException UnexpectedValueException
     */
    function testNotNull_withNull() {
        Check::notNull(null);
        $this->fail();
    }

    function testNotNullFormatsMessageLazy() {
        try {
            Check::notNull(null, "%d%d%d%d%d%d", 1, 2, 3, 4, 5, 6);
        } catch (\UnexpectedValueException $e) {
            $this->assertEquals('123456', $e->getMessage());
        }
    }

}
