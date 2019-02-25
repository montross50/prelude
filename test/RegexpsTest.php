<?php

namespace Prelude;

class RegexpsTest extends \PHPUnit_Framework_TestCase {

    function test_test() {
        $tests = array('/abc/', '/[cba]+/');

        $mustPass = '---abc---';
        $mustFail = 'test';

        forEach ($tests as $re) {
            $this->assertTrue (Regexps::test($re, $mustPass), "Regexps::test($re, $mustPass)");
            $this->assertFalse(Regexps::test($re, $mustFail), "Regexps::test($re, $mustFail)");
        }
    }

    function test_match() {
        $value = "0123abc456";
        $testList = array(
            '#zzzz#' => null,
            "#[a-z]{3}#" => array('abc'),
            '#a(b)c#' => array('abc', 'b'),
            '#a(b(c))#' => array('abc', 'bc', 'c'),
            '#a(b)(c)#' => array('abc', 'b', 'c'),
            '#a((b)(c))#' => array('abc', 'bc', 'b', 'c'),
            "#\d(\d)\d([a-z]+)\d(\d)#" => array("123abc45", 2, "abc", 5),
        );

        forEach ($testList as $re => $result) {
            $this->assertEquals($result,
                                Regexps::match($re, $value),
                               "Regexps::match($re, $value) failed");
        }
    }

    function test_matchAll() {
        $value = 'abc bcd cde def';

        $testList = array(
            '#[0-9]+#' => null,

            '#\w{3}#' => array(
                array('abc'),
                array('bcd'),
                array('cde'),
                array('def')
            ),
            '#(\w)\w(\w)#' => array(
                array('abc', 'a', 'c'),
                array('bcd', 'b', 'd'),
                array('cde', 'c', 'e'),
                array('def', 'd', 'f')
            ),
        );

        forEach ($testList as $re => $result) {
            $this->assertEquals($result,
                                Regexps::matchAll($re, $value),
                               "Regexps::matchAll($re, $value) failed");
        }
    }
}
