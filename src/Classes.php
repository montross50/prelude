<?php

namespace Prelude;

final class Classes {

    /**
     * @param string $name a class, interface, or trait name
     * @param boolean $autoload [optional]
     *
     * @return boolean
     */
    static function exists($name, $autoload=true) {
        return Classes::isClass($name, $autoload) or
               Classes::isInterface($name, $autoload) or
               Classes::isTrait($name, $autoload);
    }

    /**
     * @param string $name a class, interface, or trait name
     * @return boolean `true` if the given name has been already defined.
     */
    static function isDefined($name) {
        return Classes::exists($name, $autoload = false);
    }

    /**
     * @param string $className
     * @param boolean $autoload [optional]
     *
     * @return boolean `true` if the className is an existing class
     */
    static function isClass($className, $autoload=true) {
        return class_exists($className, $autoload);
    }

    /**
     * @param string $className
     * @param boolean $autoload [optional]
     *
     * @return boolean `true` if the className is an existing interface
     */
    static function isInterface($className, $autoload=true) {
        return interface_exists($className, $autoload);
    }

    /**
     * Traits were included in php 5.4.0, thus this function
     * will safely return `false` when running on php < 5.4
     * @link http://php.net/trait
     *
     * @param string $className
     * @param boolean $autoload [optional]
     *
     * @return boolean `true` if the className is an existing trait
     */
    static function isTrait($className, $autoload=true) {
        return function_exists('trait_exists') and
               trait_exists($className, $autoload);
    }

    /**
     * Returns the simple class name for the given $className
     *
     * @example
     *    shortName(ArrayAccess) -> ArrayAccess
     *    shortName(Bar\Baz\Foo) -> Foo
     *
     * @param mixed $className
     * @return string
     */
    static function shortName($className) {
        if (is_object($className)) {
            $className = Objects::classOf($className);
        }
        return substr($className, Strings::lastIndexOf($className, '\\') + 1);
    }

    /**
     * Returns the namespace of the given class' name
     *
     * @example
     *    shortName(ArrayAccess) -> null
     *    shortName(Bar\Baz\Foo) -> Bar\Baz
     *
     * @param string|object className
     * @return string the namespace portion, or `null` for classes in the global scope
     */
    static function namespace_($className) {

        if (is_object($className)) {
            $className = Objects::classOf($className);
        }

        Check::argument(is_string($className));

        $index = Strings::lastIndexOf($className, '\\');

        if ($index !== -1) {
            $ns = substr($className, 0, $index);
            return ltrim($ns, '\\');
        }
    }

    /**
     * Returns `true` if $class extends $parent
     *
     * @example: given the following hierarchy:
     *
     *    class Cat extends Animal implements Carnivore
     *    class Siamese extends Cat
     *
     *    extends_(Siamese, Cat) == true
     *    extends_(Siamese, Animal) == true
     *    extends_(Siamese, Carnivore) == false
     *
     * @param string|object $class
     * @param string $extends
     *
     * @return boolean
     */
    static function extends_($class, $extends) {
        $parents = class_parents($class);
        return $parents and Arrays::containsKey($parents, $extends);
    }

    /**
     * Returns `true` if $class implements $interface
     *
     * @example: given the following hierarchy:
     *
     *    class Dog implements Carnivore
     *    class Labrador extends Dog implements Domestic
     *
     *    implements_(Labrador, Domestic) == true
     *    implements_(Labrador, Carnivore) == true
     *    implements_(Labrador, Dog) == false
     *
     * @param string|object $class
     * @param string $interface
     *
     * @return boolean
     */
    static function implements_($class, $interface) {
        $parents = class_implements($class);
        return $parents and Arrays::containsKey($parents, $interface);
    }
}
