<?php

namespace Prelude;

class ClassesTest extends \PHPUnit_Framework_TestCase {

    /**
     * @dataProvider provider_exists
     */
    function test_exists($exists, $class) {
        $this->assertEquals($exists, Classes::exists($class));
    }

    /**
     * @dataProvider provider_exists
     */
    function test_isDefined($exists, $class) {
        $this->assertEquals($exists, Classes::isDefined($class));
    }

    function provider_exists() {
        return array(
            array(1, __CLASS__),
            array(0, 'I-haz-no-clazz'),
        );
    }

    function test_shortName() {
        $this->assertEquals('ClassesTest', Classes::shortName(__CLASS__));
        $this->assertEquals('ClassesTest', Classes::shortName($this));
    }

    /**
     * @dataProvider provider_namespace
     */
    function test_namespace($namespace, $class) {
        $this->assertEquals($namespace, Classes::namespace_($class));
    }


    function provider_namespace() {
        return array(
            array(__NAMESPACE__, __CLASS__),
            array(__NAMESPACE__, $this),

            // global interface
            array(null, 'ArrayAccess'),

            // global classes
            array(null, 'ArrayObject'),
            array(null, new \ArrayObject),

            // namespaced classes
            array('a',   'a\\b'),
            array('a', '\\a\\b'),

            array('a\\b\\c',   'a\\b\\c\\d'),
            array('a\\b\\c', '\\a\\b\\c\\d'),
        );
    }

    /**
     * @dataProvider prov_extends
     */
    function test_extends($class, $parent) {
        $this->assertTrue(
            Classes::extends_($class, $parent)
        );
    }

    function prov_extends() {

        return array(
            array(__CLASS__, 'PHPUnit_Framework_TestCase'),
            array($this, 'PHPUnit_Framework_TestCase'),

            array(__CLASS__ . '_B', __CLASS__ . '_A'),
            array(__CLASS__ . '_BI', __CLASS__ . '_A'),
            array(__CLASS__ . '_C', __CLASS__ . '_B'),
            array(__CLASS__ . '_C', __CLASS__ . '_A'),
        );
    }

    /**
     * @dataProvider prov_notExtends
     */
    function test_notExtends($class, $parent) {
        $this->assertFalse(
            Classes::extends_($class, $parent)
        );
    }

    function prov_notExtends() {
        return array(

            array(__CLASS__, __CLASS__),
            array($this, __CLASS__),

            array(__CLASS__ . '_A' , __CLASS__ . '_B'),
            array(__CLASS__ . '_BI', __CLASS__ . '_I'),
            array(__CLASS__ . '_I' , __CLASS__ . '_A'),
            array(__CLASS__ . '_I' , __CLASS__ . '_I'),
        );
    }


    /**
     * @dataProvider prov_implements
     */
    function test_implements($class, $parent) {
        $this->assertTrue(
            Classes::implements_($class, $parent)
        );
    }

    function prov_implements() {
        $obj = new ClassesTest_BI();
        return array(
            array(get_class($obj), __CLASS__ . '_I'),
            array($obj, __CLASS__ . '_I'),

            array(__CLASS__ . '_JI', __CLASS__ . '_I'),
            array(__CLASS__ . '_JI', __CLASS__ . '_J'),

            array(__CLASS__ . '_K', __CLASS__ . '_I'),
            array(__CLASS__ . '_K', __CLASS__ . '_J'),
            array(__CLASS__ . '_K', __CLASS__ . '_JI'),
        );
    }

    /**
     * @dataProvider prov_notImplements
     */
    function test_notImplements($class, $parent) {
        $this->assertFalse(
            Classes::implements_($class, $parent)
        );
    }

    function prov_notImplements() {
        $obj = new ClassesTest_BI();
        return array(
            array($this, __CLASS__),
            array(__CLASS__, __CLASS__),

            array(__CLASS__ . '_I' , __CLASS__ . '_I'),
            array(__CLASS__ . '_I' , __CLASS__ . '_J'),
            array(__CLASS__ . '_I', __CLASS__ . '_JI'),

            array($obj, __CLASS__ . '_J'),
            array($obj, __CLASS__ . '_JI'),
            array($obj, __CLASS__ . '_A'),
            array($obj, __CLASS__ . '_B'),
            array($obj, __CLASS__ . '_BI'),
            array($obj, __CLASS__ . '_C'),
        );
    }
}

interface ClassesTest_I { }
interface ClassesTest_J { }
interface ClassesTest_JI extends ClassesTest_J, ClassesTest_I { }
interface ClassesTest_K extends ClassesTest_JI { }

class ClassesTest_A { }
class ClassesTest_B  extends ClassesTest_A { }
class ClassesTest_BI extends ClassesTest_B implements ClassesTest_I { }
class ClassesTest_C  extends ClassesTest_B { }
