<?php

namespace Prelude;

class IterablesTest extends \PHPUnit_Framework_TestCase {

    /**
     * @dataProvider iterables
     */
    function testIsIterable($iterable) {
        $this->assertTrue(Iterables::isIterable($iterable));
    }

    /**
     * @dataProvider nonIterables
     */
    function testIsNotIterable($thing) {
        $this->assertFalse(Iterables::isIterable($thing));
    }

    function iterables() {
        return array_merge($this->emptyIterables(),
                           $this->nonEmptyIterables());
    }

    function emptyIterables() {
        return array(
            array(array()),
            array(new \EmptyIterator),
            array(new \ArrayIterator),
            array(new \ArrayObject),
        );
    }

    function nonEmptyIterables() {
        return array(
            array(array(1, 2)),
            array(new \ArrayIterator(array(1, 2))),
            array(new \ArrayIterator(array(array()))),
            array(new \DirectoryIterator(__DIR__)),
            array(new \ArrayObject(array(1, 2, 3))),
        );
    }

    function nonIterables() {
        return array(
            array(null),
            array(true),
            array(false),
            array(new \StdClass),
            array(1),
            array(12),
            array(12.56),
            array(''),
            array('test'),
            array($this),
            array(fopen(__FILE__, 'r')),
        );
    }

    /**
     * @dataProvider emptyIterables
     */
    function testIsEmpty_withEmpty($iterable) {
        $this->assertTrue(Iterables::isEmpty($iterable));
    }

    /**
     * @dataProvider nonEmptyIterables
     */
    function testIsEmpty_withNonEmpty($iterable) {
        $this->assertFalse(Iterables::isEmpty($iterable));
    }

    /**
     * @dataProvider nonIterables
     * @expectedException InvalidArgumentException
     */
    function testIsEmpty_withNonIterables($thing) {

        Iterables::isEmpty($thing);
        $this->fail();
    }

    function testToArray() {
        $values = array(
            1,
            'a' => 2,
            __DIR__ => __FILE__
        );
        $this->assertSame($values, Iterables::toArray($values));
        $this->assertEquals($values, Iterables::toArray(new \ArrayIterator($values)));
    }

    /**
     * @dataProvider iterables
     */
    function testToArray_withIterable($iterable) {
        $array = Iterables::toArray($iterable);
        $this->assertInternalType('array', $array);

        if (is_array($iterable)) {
            $this->assertCount(count($iterable), $array);
        } else {
            $this->assertCount(iterator_count($iterable), $array);
        }
    }

    /**
     * @dataProvider nonIterables
     * @expectedException InvalidArgumentException
     */
    function testToArray_withNonIterable($thing) {
        Iterables::toArray($thing);
        $this->fail();
    }

    function testToIterable() {
        $values = array(1, 2, 3);
        $iterator = new \ArrayIterator($values);

        $this->assertSame($iterator, Iterables::toIterable($iterator));
        $this->assertEquals($iterator, Iterables::toIterable($values));
    }

    /**
     * @dataProvider iterables
     */
    function testToIterable_withIterable($iterable) {
        $this->assertInstanceOf('Traversable', Iterables::toIterable($iterable));
    }

    /**
     * @dataProvider nonIterables
     * @expectedException InvalidArgumentException
     */
    function testToIterable_withNonIterable($thing) {
        Iterables::toIterable($thing);
        $this->fail();
    }

    function testToIterator() {
        $values = array(1, 2, 3);
        $iterator = new \ArrayIterator($values);

        $this->assertSame($iterator, Iterables::toIterator($iterator));
        $this->assertEquals($iterator, Iterables::toIterator($values));
    }

    /**
     * @dataProvider iterables
     */
    function testToIterator_withIterable($iterable) {
        $this->assertInstanceOf('Iterator', Iterables::toIterator($iterable));
    }

    /**
     * @dataProvider nonIterables
     * @expectedException InvalidArgumentException
     */
    function testToIterator_withNonIterable($thing) {
        Iterables::toIterator($thing);
        $this->fail();
    }

}

