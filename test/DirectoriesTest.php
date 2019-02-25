<?php

namespace Prelude;

class DirectoriesTest extends \PHPUnit_Framework_TestCase {

    function test_childs() {
        $this->assertInternalType('array', Directories::childs(__DIR__));
        $this->assertInternalType('array', Directories::childs(__FILE__));

        $this->assertTrue (in_array(__FILE__, Directories::childs(__DIR__ )));
        $this->assertTrue (in_array(__FILE__, Directories::childs(__FILE__)));
    }

    function test_subdirs() {
        $parent = Directories::subdirs(Paths::base(__DIR__));
        $this->assertInternalType('array', $parent);

        $this->assertTrue (in_array(__DIR__ , $parent));
        $this->assertFalse(in_array(__FILE__, $parent));
    }

    function test_files() {
        $files = Directories::files(__FILE__);
        $this->assertInternalType('array', $files);

        forEach ($files as $i => $file) {
            $this->assertTrue(is_int($i));
            $this->assertStringStartsWith(__DIR__, $file);
        }
        $this->assertTrue(in_array(__FILE__, $files));
    }
}
