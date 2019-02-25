<?php

namespace Prelude;

final class Closures {

    /**
     * Creates a version of the function that can only be called one time.
     *
     * Repeated calls to the modified function will have no effect, returning
     * the value from the original call.
     *
     * @param \Closure callable
     * @return \Closure
     */
    static function once(\Closure $callable) {
        return function() use (& $callable) {
            static $value;
            if ($callable) {
                $value = call_user_func_array($callable, func_get_args());
                $callable = null;
            }
            return $value;
        };
    }

    /**
     * Memoize the given function by catching the computed result.
     *
     * Useful for speeding up slow running computation.
     *
     * @param \Closure $callable
     * @param \Closure $toHash
     * @return \Closure
     */
    static function memoize(\Closure $callable, \Closure $toHash=null) {

        return function() use ($callable, $toHash) {

            static $memory = array();

            if ($toHash) {
                $hash = $toHash(func_get_args());
            } else {
                $hash = Values::hashOf(func_get_args());
            }

            if (Arrays::containsKey($memory, $hash)) {
                return $memory[$hash];
            }

            return $memory[$hash] = call_user_func_array($callable, func_get_args());
        };
    }
}
