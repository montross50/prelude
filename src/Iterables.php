<?php

namespace Prelude;

final class Iterables {

    /**
     * Returns `true` if $value is a valid argument
     * for a `foreach` statement
     *
     * @param mixed $value
     * @return boolean
     */
    static function isIterable($value) {
        return is_array($value) or $value instanceof \Traversable;
    }

    /**
     * @param array|\Traversable $iterable
     * @return boolean
     *
     * @throws \InvalidArgumentException if `$iterable` is not a valid iterable
     */
    static function isEmpty($iterable) {

        if (is_array($iterable)) {
            return empty($iterable);
        }

        if ($iterable instanceof \Traversable) {
            return iterator_count($iterable) === 0;
        }

        throw Iterables::NotIterable();
    }

    /**
     * @param \Traversable|array iterables
     * @return array
     *
     * @throws \InvalidArgumentException if `$iterable` is not a valid iterable
     */
    static function toArray($iterable) {

        if (is_array($iterable)) {
            return $iterable;
        }

        if ($iterable instanceof \Traversable) {
            return iterator_to_array($iterable, true); # keepKeys
        }

        throw Iterables::NotIterable();
    }

    /**
     * @param array|\Traversable $iterable
     * @return \Traversable
     *
     * @throws \InvalidArgumentException if `$iterable` is not a valid iterable
     */
    static function toIterable($iterable) {

        if (is_array($iterable)) {
            return new \ArrayIterator($iterable);
        }

        if ($iterable instanceof \Traversable) {
            return $iterable;
        }

        throw Iterables::NotIterable();
    }

    /**
     * @param array|\Traversable $iterable
     * @return \Iterator
     *
     * @throws \InvalidArgumentException if `$iterable` is not a valid iterable
     */
    static function toIterator($iterable) {

        if (is_array($iterable)) {
            return new \ArrayIterator($iterable);
        }

        if ($iterable instanceof \Iterator) {
            return $iterable;
        }

        if ($iterable instanceof \IteratorAggregate) {
            return $iterable->getIterator();
        }

        // @codeCoverageIgnoreStart
        // FIXME: allow coverage once HHVM fix the `null object` on PDOStatement's
        if ($iterable instanceof \Traversable) {
            return new \IteratorIterator($iterable);
        }
        // @codeCoverageIgnoreEnd

        throw Iterables::NotIterable();
    }

    /**
     * @return \InvalidArgumentException
     * @throws \InvalidArgumentException
     */
    private static function NotIterable() {
        throw new \InvalidArgumentException('argument is not iterable');
    }
}

