<?php

namespace Prelude;

final class Booleans {

    /**
     * Parses $value as a boolean value
     *
     * Danger!: <code>
     *     Booleans::parse('false') === false
     * </code>
     *
     * @param string $value
     * @return boolean
     */
    static function parse($value) {
        if (is_string($value)) {
            return $value and 'false' !== trim($value);
        }
        return (bool) $value;
    }

    /**
     * @param boolean $value
     * @return string
     */
    static function toString($value) {
        return $value ? 'true' : 'false';
    }
}
