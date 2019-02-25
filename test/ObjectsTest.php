<?php

namespace Prelude;


class ObjectsTest extends \PHPUnit_Framework_TestCase {

    function test_classOf() {
        $this->assertNull(Objects::classOf(__CLASS__));
        $this->assertEquals(__CLASS__, Objects::classOf($this));
    }
}
