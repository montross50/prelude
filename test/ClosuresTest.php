<?php

namespace Prelude;

class ClosuresTest extends \PHPUnit_Framework_TestCase {

    function test_once() {
        $counter = 0;

        $fn = Closures::once(function () use (& $counter) {
            return ++$counter;
        });

        $this->assertEquals(0, $counter);
        $fn();
        $fn();
        $fn();
        $fn();
        $fn();
        $fn();
        $fn();
        $this->assertEquals(1, $counter);
    }

    /**
     * @dataProvider prov_onceParams
     */
    function test_onceParams() {

        $params = func_get_args();

        $fn = Closures::once(function () {
            return func_get_args();
        });

        $this->assertSame($params, call_user_func_array($fn, $params));
    }

    function prov_onceParams() {
        return array(
            array(1),
            array(null, true, false),
            array('the', 'test', 'is' => 'here'),
        );
    }

    function test_memoize() {
        $called = array();

        $fn = Closures::memoize(function ($i)  use(& $called){
            $called[$i] += 1;
            return $i * $i;
        });

        foreach (range(0, 100) as $i) {
            $called[$i] = 0;
            /** @noinspection PhpUnusedLocalVariableInspection */
            foreach (range(0, 10) as $j) {
                $r = $fn($i);
                $this->assertEquals($r, $i * $i);
            }
            $this->assertEquals(1, $called[$i]);
        }
    }

    function testMemoize_toHashIsUsed() {

        $toHash  = function() {
            return 1;
        };

        $toValue = function ($i) {
            return $i;
        };

        $fn = Closures::memoize($toValue, $toHash);

        foreach (range(0, 100) as $i) {
            $this->assertEquals(0, $fn($i));
        }
    }
}
