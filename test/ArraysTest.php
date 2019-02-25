<?php

namespace Prelude;

class ArraysTest extends \PHPUnit_Framework_TestCase {

    /**
     * @return array $values, $key, $value, $default
     */
    function matchGetters() {

        $list = array('a', 'b', 'c');
        $map  = array('a' => 1,
                      'b' => 2);

        return array(
            array($list, 0, 'a'),
            array($list, 1, 'b', 'z'),
            array($map, 'a', 1),
            array($map, 'a', 1, 2),
            array($map, 'b', 2),
            array($map, 'b', 2, 3),
            # keys not found
            array($list, 9    , null),
            array($list, false, null),
            array($list, null , null),
            array($list, 'k'  , null),
            array($map, 'z', null),
            array($map,  9 , null),
            array($map, false, null),
            array($map, null , null),
            # default values
            array($list, 'k', 'd', 'd'),
            array($map, 'z', 'd', 'd'),
        );
    }

    /**
     * @dataProvider matchGetters
     */
    function testGet(array $values, $key, $result, $default = null) {
        $this->assertEquals($result, Arrays::get($values, $key, $default));
    }

    function testGetOrCall() {
        $counter = 0;
        $closure = function () use (& $counter) {
            return ++$counter;
        };

        $this->assertNull(Arrays::getOrCall(array(0 => null), 0, $closure));
        $this->assertEquals(0, $counter);

        $this->assertEquals(1, Arrays::getOrCall(array(), 0, $closure));
        $this->assertEquals(1, $counter);

        $this->assertEquals(2, Arrays::getOrCall(array(), null, $closure));
        $this->assertEquals(2, $counter);
    }

    /**
     * @dataProvider matchGetters
     */
    function testGetOrThrow_withGoodGetters(array $values, $key, $result) {
        $key = (string) $key;
        $values[$key] = $result;
        $this->assertSame($result, Arrays::getOrThrow($values, $key));
    }

    /**
     * @dataProvider matchGetters
     * @expectedException RuntimeException
     */
    function testGetOrThrow_withBadGetters(array $values, $key) {
        if (Arrays::containsKey($values, $key)) {
            unset($values[$key]);
        }
        Arrays::getOrThrow($values, $key);
        $this->fail();
    }

    /**
     * @dataProvider matchGetters
     * @expectedException DomainException
     */
    function testGetOrThrow_withBadGetters_andException(array $values, $key) {
        if (Arrays::containsKey($values, $key)) {
            unset($values[$key]);
        }
        Arrays::getOrThrow($values, $key, new \DomainException);
        $this->fail();
    }

    function testIndexOf() {
        $values = array(
            'a' =>  1,
             1  =>  false,
          /* 2 */  'a',
          /* 3 */   0
        );

        $this->assertFalse(Arrays::indexOf($values, null));
        $this->assertFalse(Arrays::indexOf($values, true));
        $this->assertFalse(Arrays::indexOf($values, array()));

        $this->assertEquals('a', Arrays::indexOf($values,  1    ));
        $this->assertEquals( 1 , Arrays::indexOf($values,  false));
        $this->assertEquals( 2 , Arrays::indexOf($values, 'a'   ));
        $this->assertEquals( 3 , Arrays::indexOf($values,  0    ));
    }

    function testKeys() {
        $this->assertEquals(array('a', 'b'), Arrays::keys(array(
            1  => 1,
           'a' => 2,
            4  => 3,
           'b' => 4,
            5  => 5,
            false => 12
        )));
    }

    /**
     * @dataProvider keysWithList
     */
    function testKeys_withList(array $list) {
        $this->assertEquals(null, Arrays::keys($list));
    }

    function keysWithList() {
        return array(
            array(array()),
            array(array(1, 2, 3)),
            array(array(5 => 12, '6' => 20)),
        );
    }

    function testOffsets() {
        $this->assertEquals(array(1, 3, 5, 0), Arrays::offsets(array(
            1  => 1,
           'a' => 2,
            3  => 3,
           'b' => 4,
            5  => 5,
            false => 12 # php will cast `false` as `0`
        )));
    }

    /**
     * @dataProvider offsetsWithMap
     */
    function testOffsets_withMap(array $map) {
        $this->assertEquals(null, Arrays::offsets($map));
    }

    function offsetsWithMap() {
        return array(
            array(array()),
            array(array('a' => 'b')),
            array(array('a' => 'b', 'c' => 'd')),
        );
    }

    function validKeys() {
        return array(
            array(true),
            array(false),
            array(0),
            array(12),
            array(12.23),
            array(''),
            array('test'),
        );
    }

    function invalidKeys() {
        return array(
            array(null),
            array(array()),
            array(new \StdClass),
            array(new \ArrayIterator),
            array($this),
            array(fopen(__FILE__, 'r')),
        );
    }

    /**
     * @dataProvider validKeys
     */
    function testIsValidKey_withValidKeys($key) {
        $this->assertTrue(Arrays::isValidKey($key));
    }

    /**
     * @dataProvider invalidKeys
     */
    function testIsValidKey_withInvalidKeys($key) {
        $this->assertFalse(Arrays::isValidKey($key));
    }

    /**
     * @dataProvider matchGetters
     */
    function testContains(array $values, $key, $value) {
        $values[$key] = $key;
        $this->assertTrue(Arrays::contains($values, $key));
        $this->assertTrue(Arrays::contains(array($key), $key));
        $this->assertTrue(Arrays::contains(array((boolean) $key), $key, false)); // non-strict
    }

    function test_containsKey() {
        $this->assertFalse(Arrays::containsKey(array(), 12));
        $this->assertFalse(Arrays::containsKey(array(), null));
        $this->assertFalse(Arrays::containsKey(array(), false));

        $this->assertTrue(Arrays::containsKey(array(12 => 1), 12));
        $this->assertTrue(Arrays::containsKey(array(12 => 1), '12'));
        $this->assertTrue(Arrays::containsKey(array('12' => 1), 12));

        $this->assertFalse(Arrays::containsKey(array(), null));
        $this->assertFalse(Arrays::containsKey(array(), false));
    }

    /**
     * @dataProvider providerHead
     */
    function testHead($head, array $values) {
        $copy = $values + array();
        $this->assertEquals($head, Arrays::head($values));
        $this->assertEquals($copy, $values);
    }

    /**
     * @return $head, array $values
     */
    function providerHead() {
        return array(
            array(1, array(1)),
            array(1, array(1, 2)),
            array(1, array(1, 2, 3)),

            array('a', array(5 => 'a')),
            array('a', array('b' => 'a', 12 => 2)),
        );
    }

    /**
     * @expectedException UnexpectedValueException
     */
    function testHead_itThrows() {
        Arrays::head(array());
        $this->fail();
    }

    /**
     * @dataProvider provider_tail
     */
    function test_tail(array $tail, array $values) {
        $copy = $values + array();
        $this->assertEquals($tail, Arrays::tail($values));
        $this->assertEquals($copy, $values);
    }

    function provider_tail() {
        return array(
            array(array( ), array(    )),
            array(array( ), array(0   )),
            array(array(1), array(0, 1)),
            array(array( ), array('a' => 1)),
            array(array('a' => 1   ), array(       0, 'a' => 1   )),
            array(array('a' => 2, 3), array(       1, 'a' => 2, 3)),
            array(array('b' => 2   ), array('a' => 1, 'b' => 2   )),
            array(array('b' => 2, 3), array('a' => 1, 'b' => 2, 3)),
        );
    }

    function test_sortyBy() {
        $values = array(6, 5, 3, 1, 10);
        $by = function ($num) {
            return $num;
        };

        $clone = $values + array();
        $this->assertEquals(array(1, 3, 5, 6, 10), Arrays::sortBy($values, $by));
        $this->assertEquals($clone, $values, "Arrays::sortBy should not modify its input");
    }

    function providerRamdomArrays() {
        return array(
            array(array(1, 2, 3, 4)),
            array(array(1, 2, 5 => 3, 4)),
            array(array('a' => 'a', 'b' => 'b')),
            array(array('a' => 1, 'b' => 2)),
            array(array(1, 'a' => 2, 'b' => 3, 4)),
        );
    }

    /**
     * @dataProvider providerRamdomArrays
     */
    function testRandomKey(array $values) {
        for ($i=0, $times = count($values) * 2; $i < $times; $i++) {
            $this->assertArrayHasKey(Arrays::randomKey($values), $values);
        }
    }

    /**
     * @dataProvider providerRamdomArrays
     */
    function testRandomValue(array $values) {
         for ($i=0, $times = count($values) * 2; $i < $times; $i++) {
            $this->assertTrue(in_array(Arrays::randomValue($values), $values));
        }
    }

    /**
     * @expectedException UnexpectedValueException
     */
    function testRandomKey_withEmptyArray() {
        Arrays::randomKey(array());
        $this->fail();
    }


    /**
     * @expectedException UnexpectedValueException
     */
    function testRandomValue_withEmptyArray() {
        Arrays::randomValue(array());
        $this->fail();
    }

    /**
     * @dataProvider providerMerge
     */
    function testMerge($merged, array $arrays = null) {
        $this->assertEquals($merged, call_user_func_array('Prelude\\Arrays::merge', $arrays));
    }

    function providerMerge() {
        return array(
            # many null always returns null
            array(null, array()),
            array(null, array(null)),
            array(null, array(null, null)),
            array(null, array(null, null, null)),

            # empty arrays are skip
            array(null, array(
                array(),
                array(),
                array(),
            )),

            # values are transformed into arrays
            array(array(1), array(1)),
            array(array(1, 2, 3), array(1, 2, 3)),
            array(array('a', 'b'), array('a', 'b')),

            # order is guaranteed
            array(array(1, 2, 3), array(
                array(1),
                2,
                array(3)
            )),

            # keys are only added once
            array(array('a' => 'b'), array(
                    array('a' => 'b'),
                    array('a' =>  2 )
            )),
        );
    }
}
