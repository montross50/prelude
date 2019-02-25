<?php

namespace Prelude;

class OutputTest extends \PHPUnit_Framework_TestCase {

    private function ensureNoOuput(\Closure $callable) {
        ob_start();
        $level = ob_get_level();
        try {
            $callable($this);
        } catch(\Exception $e) {
            ob_end_clean();
            throw $e;
        }
        $this->assertEquals($level, ob_get_level());
        $this->assertEquals('', ob_get_clean());
    }

    /**
     * @dataProvider provider_outputs
     */
    function test_capture($output, $fn) {
        $this->ensureNoOuput(function ($that) use ($output, $fn) {
            $that->assertEquals($output, Output::capture($fn));
        });
    }

    /**
     * @dataProvider provider_outputs
     */
    function test_passthru($output, $fn) {
        $this->ensureNoOuput(function ($that) use ($output, $fn) {
            ob_start();
            try {
                $passthru = Output::passthru($fn);
            } catch (\Exception $e) {
                ob_end_clean();
                throw $e;
            }
            $that->assertEquals($output, ob_get_clean());
            $that->assertEquals($output, $passthru);
        });
    }

    function provider_outputs() {
        return array(
            array(null    , function () {                }),
            array(''      , function () { echo '';       }),
            array('output', function () { echo 'output'; }),
        );
    }

    function test_capture_exceptions() {
        $level = ob_get_level();
        $exception = new \Exception();

        try {
            $result = Output::capture(function () use ($exception) {
                echo 12;
                throw $exception;
            });
            $this->fail('Output::capture should not catch Exceptions');
        } catch (\Exception $e) {
            $this->assertFalse(isset($result));
            $this->assertSame($e, $exception);
        }
        $this->assertEquals($level, ob_get_level());
    }

    function test_passthru_exceptions() {
        $level = ob_get_level();
        $exception = new \Exception();

        try {
            $result = Output::passthru(function () use ($exception) {
                echo 12;
                throw $exception;
            });
            $this->fail('Output::passthru should not catch Exceptions');
        } catch (\Exception $e) {
            $this->assertFalse(isset($result));
            $this->assertSame($e, $exception);
        }

        $this->assertEquals($level, ob_get_level());
    }

    function test_captureAll() {
        $f1 = function () { echo 1; };
        $f2 = function () { echo 2; };
        $f3 = function () { echo 3; };

        $this->assertEquals('123', Output::captureAll($f1, $f2, $f3));
        $this->assertEquals('123', Output::captureAll(array($f1, $f2, $f3)));
    }

    function test_captureAll_exceptions() {
        $level = ob_get_level();
        $exception = new \Exception();
        $count = 0;

        $counter = function () use (&$count) {
            echo ++$count;
        };
        $throws = function () use ($exception) {
            throw $exception;
        };

        try {
            $result = Output::captureAll($counter, $counter, $throws, $counter);
            $this->fail('Output::captureAll should not catch Exceptions');
        } catch (\Exception $e) {
            $this->assertFalse(isset($result));
            $this->assertSame($e, $exception);
        }

        $this->assertEquals(2, $count);
        $this->assertEquals($level, ob_get_level());
    }
}
