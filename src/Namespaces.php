<?php

namespace Prelude;

final class Namespaces {

    /**
     *
     * Example:
     *  join(\Some\Namespace, \With\Other\) === Some\Namespace\With\Other
     *
     * @param string ...$fragments
     * @return string the resultant namespace
     */
    static function join() {
        $fragments = array_map(function ($frag) {
            if ($frag = trim($frag, '\\')) {
                return $frag;
            }
        }, func_get_args());

        return trim(implode('\\', $fragments), '\\');
    }
}
