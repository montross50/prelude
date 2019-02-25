<?php

namespace Prelude;

final class Values {

    /**
     * @param mixed $value
     * @return string
     */
    static function typeOf($value) {
        return strtolower(gettype($value));
    }

    /**
     * Creates the unique hash of the given value
     *
     * @param mixed $value
     * @return string
     */
    static function hashOf($value) {

        Check::argument(!is_resource($value), 'cannot hash resources');

        if (is_object($value)) {
            return spl_object_hash($value);
        }

        return md5(serialize($value));
    }

    /**
     * @param mixed $value
     * @return boolean
     */
    static function isEmpty($value) {

        if (Iterables::isIterable($value)) {
            return Iterables::isEmpty($value);
        }

        return empty($value);
    }
}
