<?php

namespace Prelude;

final class Regexps {

    /**
     * @param string $regexp
     * @param string $value
     * @return boolean
     */
    static function test($regexp, $value) {
        return (bool) preg_match($regexp, $value);
    }

    /**
     * @param string $regexp
     * @param string $value
     * @return array
     */
    static function match($regexp, $value) {
        if (preg_match($regexp, $value, $matches)) {
            return $matches;
        }
    }

    /**
     * @param string $regexp
     * @param string $value
     * @return array
     */
    static function matchAll($regexp, $value) {
        if (preg_match_all($regexp, $value, $matches, PREG_SET_ORDER)) {
            return $matches;
        }
    }
}
