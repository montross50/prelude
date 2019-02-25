<?php

namespace Prelude;

final class Check {

    /**
     * @param mixed $expr
     * @param string $message [optional]
     * @param string... $arguments
     *
     * @throws \InvalidArgumentException if $expr is `falsy`
     */
    static function argument($expr, $message=null) {
        if (!$expr) {
            if (func_num_args() > 2) {
                $message = call_user_func_array('sprintf', Arrays::tail(func_get_args()));
            }
            throw new \InvalidArgumentException($message);
        }
    }

    /**
     * @param boolean $expr
     * @param string $message [optional]
     * @param string... arguments
     *
     * @throws \DomainException if $expr is `falsy`
     */
    static function state($expr, $message=null) {
        if (!$expr) {
            if (func_num_args() > 2) {
                $message = call_user_func_array('sprintf', Arrays::tail(func_get_args()));
            }
            throw new \DomainException($message);
        }
    }

    /**
     * @param mixed $value
     * @param string $message [optional]
     * @param string... arguments
     *
     * @return mixed
     *
     * @throws \UnexpectedValueException if `true` == empty($value)
     */
    static function notEmpty($value, $message=null) {
        if (Values::isEmpty($value)) {
            if (func_num_args() > 2) {
                $message = call_user_func_array('sprintf', Arrays::tail(func_get_args()));
            }
            throw new \UnexpectedValueException($message);
        }
        return $value;
    }

    /**
     * @param  mixed $value
     * @param  string $message [optional]
     * @param string... arguments
     * @return mixed
     *
     * @throws \UnexpectedValueException if $value is `null`
     */
    static function notNull($value, $message=null) {
        if (null === $value) {
            if (func_num_args() > 2) {
                $message = call_user_func_array('sprintf', Arrays::tail(func_get_args()));
            }
            throw new \UnexpectedValueException($message);
        }
        return $value;
    }
}
