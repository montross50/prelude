<?php

namespace Prelude;

final class Strings {

    /**
     *
     * @example How to use it?
     *  concat("he", "llo", " ", "wor", "ld") == "hello world"
     *
     * @param string ...$str
     * @return string
     */
    static function concat() {
        return implode('', func_get_args());
    }

    /**
     * @param string $value
     * @param string $separator
     *
     * @return array
     */
    static function split($value, $separator) {
        if ($separator) {
            return explode($separator, $value);
        }
        return str_split($value);
    }

    /**
     * Returns `true` if `$value` contains `$piece`
     *
     * @param string $value
     * @param string $piece
     * @param int $offset [optional]
     *
     * @return boolean
     */
    static function contains($value, $piece, $offset=0) {
        return $piece and Strings::indexOf($value, $piece, $offset) > -1;
    }

    /**
     *
     * @param string $value
     * @param string $piece
     * @param int $offset [optional]
     *
     * @return integer
     */
    static function indexOf($value, $piece, $offset=0) {

        if ($piece) {

            if (false !== $result = strpos($value, $piece, $offset)) {
                return $result;
            }

        } elseif ($value) {
            return $offset;
        }

        return -1;
    }

    /**
     *
     * @example How to use it?
     *     lastIndexOf('abbc', 'a') == 0
     *     lastIndexOf('abbc', 'b') == 3
     *     lastIndexOf('abbc', 'c') == 4
     *
     * @param string $value
     * @param string $piece
     *
     * @return integer
     */
    static function lastIndexOf($value, $piece) {

        if ($piece) {
            if (false !== $result = strrpos($value, $piece)) {
                return $result;
            }
        } elseif ($value) {
            return strlen($value) - 1;
        }

        return -1;
    }

    /**
     * @param string $value
     * @param string $piece
     * @param int $offset [optional]
     *
     * @return boolean
     */
    static function startsWith($value, $piece, $offset=0) {
        return $piece and $offset === Strings::indexOf($value, $piece, $offset);
    }

    /**
     * @param string $value
     * @param string $piece
     *
     * @return boolean
     */
    static function endsWith($value, $piece) {
        return $piece and $piece === substr($value, -1 * strlen($piece));
    }
}
