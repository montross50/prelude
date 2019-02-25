<?php
namespace Prelude;

final class Arrays {

    /**
     * Returns `$values[$key]`, or `$default` if `$values` does not contains `$key`
     *
     * @param array $values
     * @param int|string $key
     * @param mixed $default [optional] default is `null`
     *
     * @return mixed
     */
    static function get(array $values, $key, $default = null) {

        if (Arrays::containsKey($values, $key)) {
            return $values[$key];
        }

        return $default;
    }

    /**
     * Returns `$values[$key]`, or `$callable($values, $key)` if `$values` does not contains `$key`
     *
     * If the array does not contains `$key` the callable will be
     *
     * @param array $values
     * @param int|string $key
     * @param \Closure $callable signature is `mixed function(array $values, $key)`
     *
     * @return mixed
     */
    static function getOrCall(array $values, $key, \Closure $callable) {

        if (Arrays::containsKey($values, $key)) {
            return $values[$key];
        }

        return $callable($values, $key);
    }

    /**
     * Returns `$values[$key]`, or throws if `$values` does not contains `$key`
     *
     * @param array $values
     * @param int|string $key
     * @param \Exception $e [optional]
     *
     * @return mixed the value found
     *
     * @throws \Exception when `$values` does not contains `$key`
     */
    static function getOrThrow(array $values, $key, \Exception $e = null) {

        if (Arrays::containsKey($values, $key)) {
            return $values[$key];
        }

        if ($e) {
            throw $e;
        }

        throw new \RuntimeException();
    }

    /**
     * Searches the `$values` array for a given `$value` and,
     * if successful returns the corresponding key
     *
     * @param array $values
     * @param mixed $value
     *
     * @return int|string|bool the found key, or `false` otherwise
     */
    static function indexOf(array $values, $value) {
        return array_search($value, $values, true);
    }

    /**
     * Returns if the given `$key` can be used as an array key
     *
     * Note:
     * `Arrays::isValidKey(null)` will return `false` although php allows
     * using `null` as key (by casting to `string`)
     *
     * @param mixed $key
     *
     * @return boolean
     */
    static function isValidKey($key) {
        return is_scalar($key);
    }

    /**
     * Returns a list of strings keys
     *
     * @param array $values
     *
     * @return array keys found, or `null` if `$values` does not contains string keys
     */
    static function keys(array $values) {
        if ($map = Arrays::extractMap($values)) {
            return array_keys($map);
        }
    }

    /**
     * Returns a portion of the array, whose key-value map
     *
     * @param array $values
     * @return array
     */
    static function extractMap(array $values) {
        /** @noinspection PhpUnusedParameterInspection */
        return Arrays::where($values, function ($value, $key) {
            return is_string($key);
        });
    }

    /**
     * Returns a list of integer keys of $values
     *
     * @param array values
     *
     * @return array offsets found, or `null` if `$values` does not contains integer offsets
     */
    static function offsets(array $values) {
        if ($list = Arrays::extractList($values)) {
            return array_keys($list);
        }
    }

    /**
     * @param array values
     * @return array
     */
    static function extractList(array $values) {
        /** @noinspection PhpUnusedParameterInspection */
        return Arrays::where($values, function ($value, $key) {
            return is_int($key);
        });
    }

    /**
     * Returns `true` if the `$values` array contains the `$value` element
     *
     * Unlike `in_array`, this function handles empty arrays without generating notices.
     *
     * @param array $values
     * @param mixed $value
     * @param boolean $strict [optional] default is `true`
     *
     * @return boolean
     */
    static function contains(array $values, $value, $strict = true) {
        return $values and in_array($value, $values, $strict);
    }

    /**
     * Returns `true` if the `$values` array contains the `$key` key
     *
     * Unlike `array_key_exists` this function handles non-valid keys,
     * like non-scalar values, without generating notices.
     *
     * @param array $values
     * @param mixed $key
     *
     * @return boolean
     */
    static function containsKey(array $values, $key) {
        return $values and Arrays::isValidKey($key) and array_key_exists((string) $key, $values);
    }

    /**
     * Returns the first element, aka "the head", of the values array
     *
     * @param array $values
     * @return mixed
     *
     * @throws \UnexpectedValueException if $values has not element
     */
    static function head(array $values) {
        Check::notEmpty($values);
        $slice = array_slice($values, 0, 1);
        return reset($slice);
    }

    /**
     * Returns an array of elements from `$values`, starting from the second element
     *
     * Note: Only numeric offsets will be reordered.
     *
     * @param array $values
     *
     * @return array
     */
    static function tail(array $values) {
        return array_slice($values, 1);
    }

    /**
     * Extracts a portion of the array, by applying a function to
     * determine if each value should
     *
     * @param array values
     * @param \Closure predicate signature is `boolean function($value, $key)`
     *
     * @return array|null the accepted values, or `null` if no value was accepted
     */
    static function where(array $values, \Closure $predicate) {
        $result = null;
        foreach ($values as $key => $value) {
            if ($predicate($value, $key)) {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    /**
     * Returns a sorted copy of the `$values` array.
     *
     * Each element will be converted to `int` using the `$valueOf` function.
     *
     * @param array $values the values to sort
     * @param \Closure $valueOf signature is `int function($element)`
     *
     * @return array the sorted values array
     */
    static function sortBy(array $values, \Closure $valueOf) {
        $copy = $values + array();
        usort($copy, function ($a, $b) use ($valueOf) {
            return $valueOf($a) - $valueOf($b);
        });
        return $copy;
    }

    /**
     * Picks a random key from an array
     *
     * @param array $values
     * @return int|string the chosen key
     *
     * @throws \UnexpectedValueException If `$values` array is empty
     */
    static function randomKey(array $values) {
        Check::notEmpty($values);
        return array_rand($values, 1);
    }

    /**
     * Picks a random value from an array
     *
     * @param array $values
     * @return mixed the chosen value
     *
     * @throws \UnexpectedValueException If `$values` array is empty
     */
    static function randomValue(array $values) {
        return $values[Arrays::randomKey($values)];
    }

    /**
     * @param array ...$arrays
     * @return array
     */
    static function merge() {

        $result = null;

        foreach (func_get_args() as $array) {

            if (null === $array) {
                continue;
            }

            if ($array = (array) $array) {
                if ($result) {
                    $result = array_merge($result, (array) Arrays::extractList($array));
                    $result = array_merge((array) Arrays::extractMap($array), $result);
                } else {
                    $result = $array;
                }
            }
        }

        return $result;
    }
}
