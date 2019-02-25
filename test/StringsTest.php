<?php

namespace Prelude;

class StringsTest extends \PHPUnit_Framework_TestCase {

    function test_concat() {
        $this->assertEquals('', Strings::concat());
        $this->assertEquals('', Strings::concat(null, null, null));
        $this->assertEquals('a', Strings::concat(null, 'a', null));
        $this->assertEquals('abc', Strings::concat('a', 'b', 'c'));
    }

    function test_indexOf() {
        $this->assertEquals(-1, Strings::indexOf('',    ''     ));
        $this->assertEquals(-1, Strings::indexOf('',    'z'    ));
        $this->assertEquals(-1, Strings::indexOf('',    'z'    ));
        $this->assertEquals( 0, Strings::indexOf('aba', ''     ));
        $this->assertEquals(-1, Strings::indexOf('aba', 'z'    ));
        $this->assertEquals( 0, Strings::indexOf('aba', 'a'    ));
        $this->assertEquals( 1, Strings::indexOf('aba', 'b' , 1));
        $this->assertEquals( 1, Strings::indexOf('aba', 'ba', 1));
        $this->assertEquals( 1, Strings::indexOf('aba', ''  , 1));
        $this->assertEquals( 2, Strings::indexOf('aba', 'a' , 1));
        $this->assertEquals( 1, Strings::indexOf('aba', 'ba', 1));
        $this->assertEquals(-1, Strings::indexOf('aba', 'ba', 2));
    }


    function test_lastIndexOf() {
        $this->assertEquals(-1, Strings::lastIndexOf('',    ''     ));
        $this->assertEquals(-1, Strings::lastIndexOf('',    'z'    ));
        $this->assertEquals(-1, Strings::lastIndexOf('',    'z'    ));
        $this->assertEquals(-1, Strings::lastIndexOf('aba', 'z'    ));
        $this->assertEquals( 2, Strings::lastIndexOf('aba', ''     ));
        $this->assertEquals( 2, Strings::lastIndexOf('aba', 'a'    ));
        $this->assertEquals( 1, Strings::lastIndexOf('aba', 'ba'   ));
    }

    function test_contains() {
        $this->assertTrue(Strings::contains('abc', 'b'));
        $this->assertTrue(Strings::contains('abc', 'a'));
        $this->assertTrue(Strings::contains('abc', 'c'));

        $this->assertTrue(Strings::contains('abc', 'c', 1));
        $this->assertFalse(Strings::contains('abc', 'a', 1));
    }


    function test_startsWith_with_chars() {
        $this->assertFalse(Strings::startsWith('' , ''));
        $this->assertFalse(Strings::startsWith('a', ''));
        $this->assertFalse(Strings::startsWith('' , 'a'));

        $this->assertTrue(Strings::startsWith('a' , 'a'));
    }

    function test_startsWith_with_strings() {
        $this->assertTrue(Strings::startsWith('abc' , 'a'));
        $this->assertTrue(Strings::startsWith('abc',  'ab'));
        $this->assertTrue(Strings::startsWith('abc',  'abc'));
    }

    function test_startsWith_with_offset() {
        $this->assertFalse(Strings::startsWith('abc', 'a', 1));
        $this->assertTrue(Strings::startsWith('abc' , 'b', 1));
    }

    function test_endsWith_with_chars() {
        $this->assertFalse(Strings::endsWith('' , ''));
        $this->assertFalse(Strings::endsWith('a', ''));
        $this->assertFalse(Strings::endsWith('' , 'a'));
    }

    function test_endsWith_width_strings() {
        $this->assertTrue(Strings::endsWith('abc' , 'c'));
        $this->assertTrue(Strings::endsWith('abc',  'bc'));
        $this->assertTrue(Strings::endsWith('abc',  'abc'));
    }

    function test_split() {
        $this->assertEquals(array(''), Strings::split('', 'a'));
        $this->assertEquals(array('test'), Strings::split('test', 'a'));
        $this->assertEquals(array('t', 'e', 's', 't'), Strings::split('test', ''));
        $this->assertEquals(array('t', 'st'), Strings::split('test', 'e'));
    }
}


