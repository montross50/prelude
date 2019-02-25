<?php

namespace Prelude;

/** @noinspection PhpInconsistentReturnPointsInspection */
final class Objects {

    /**
     * Returns the fully qualified class name for the given $object
     *
     * @param mixed $object
     * @return string
     */
    static function classOf($object) {
        if (is_object($object)) {
            return get_class($object);
        }
    }
}
