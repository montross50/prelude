<?php

namespace Prelude;

class NamespacesTest extends \PHPUnit_Framework_TestCase {

    function joins() {
        return array(
            array('a\\b\\c', array('a\\b\\c')),
            array('a\\b\\c', array('',    'a\\b\\c')),
            array('a\\b\\c', array('',    'a\\b\\c', '')),
            array('a\\b\\c', array('',    'a\\b\\c', null)),
            array('a\\b\\c', array(null,    'a\\b\\c', null)),
            array('a\\b\\c', array('\\',    'a\\b\\c')),
            array('a\\b\\c', array(  'a',      'b\\c')),
            array('a\\b\\c', array(  'a',    '\\b\\c')),
            array('a\\b\\c', array('\\a',    '\\b\\c')),
            array('a\\b\\c', array(  'a\\b',      'c')),
            array('a\\b\\c', array(  'a\\b',    '\\c')),
            array('a\\b\\c', array('\\a\\b',    '\\c')),
        );
    }
    /**
     * @dataProvider joins
     */
    function testJoin($result, array $pieces) {
        $this->assertEquals($result, call_user_func_array(array('Prelude\\Namespaces', 'join'), $pieces));
    }
}
