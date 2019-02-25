<?php

namespace Prelude;

/**
 *                   { location }
 *  path === drive + dir + file  === base  + file
 *  /a/b             /a     b    ===   /a/   b
 *  C:/a     C:      /      a    === C:/     a
 */
final class Paths {

    /**
     * Normalizes the slashes in the given `$path`
     *
     * @param  string $path
     * @return string
     */
    static function normalize($path) {

        if ($drive = Paths::drive($path)) {
            $path = substr($path, strlen($drive));
        }

        $path = strtr($path, '\\', '/');

        if ($path and $path !== '/') {
            return $drive . rtrim($path, '/');
        }

        return $drive . '/';
    }

    /**
     * Parses `$path` and splits its main components
     *
     * @param  string $path
     * @return array {
     *    string drive,
     *    string dir,
     *    string file
     * }
     */
    static function parse($path) {
        $info = array(
            'dir'  => Paths::dir($path),
            'file' => Paths::file($path)
        );

        if ($drive = Paths::drive($path)) {
            $info['drive'] = $drive;
        }

        return $info;
    }

    /**
     * Returns the drive portion of the given path
     *
     * This method can handle win-style paths on *nix systems
     *
     * @param  string $path
     * @return string the found drive, or `null` otherwise
     *
     * @example
     *    drive('/a/b/c') == null
     *    drive('C:/a/b') == 'C:'
     *    drive('C:')     == 'C:'
     */
    static function drive($path) {
        if ($match = Regexps::match('/^(\w\:)/', $path)) {
            return $match[1];
        }
    }

    /**
     * Returns the directory portion of the given `$path`
     *
     * @param  string $path
     * @return string
     *
     * @example
     *    dir('/') == '/'
     *    dir('a/b') == 'a'
     *    dir('C:/a/b') == '/a'
     */
    static function dir($path) {

        $location = Paths::location($path);
        $index = Strings::lastIndexOf($location, '/');

        if ($index > 0) {
            return substr($location, 0, $index);
        }

        return Paths::isAbsolute($path) ? '/' : '';
    }

    /**
     * Returns the path's drive, followed by the directory portion of the given `$path`
     *
     * @example
     *     base(   '/a/b') ==   '/a'
     *     base('C:/a/b')  == 'C:/a'
     *
     * @param string $path
     * @return string
     */
    static function base($path) {
        return Paths::drive($path) . Paths::dir($path);
    }

    /**
     * Returns the file portion of the given path
     *
     * @example
     *    file(   '/'  ) == ''
     *    file('C:/a/b') == 'b'
     *    file(   'a/b') == 'b'
     *
     * @param string $path
     * @return string the file portion, or an empty string if the path
     *                    ends with a slash
     */
    static function file($path) {
        $location = Paths::location($path);
        return substr($location, Strings::lastIndexOf($location, '/') + 1);
    }


    /**
     * Returns the given path, without the drive portion
     *
     * @example
     *     location(  '/a/b') == '/a/b'
     *     location('C:/a/b') == '/a/b'
     *
     * @param string $path
     * @return string
     */
    static function location($path) {

        if ($drive = Paths::drive($path)) {
            if (!$path = substr($path, strlen($drive)))  {
                return '/';
            }
        }

        if ($path) {
            return Paths::normalize($path);
        }
    }

    /**
     * Returns `true` if the given `$path` is absolute
     *
     * @param string $path
     * @return boolean
     */
    static function isAbsolute($path) {
        return Strings::startsWith($path, '/') or Paths::drive($path);
    }

    /**
     * Returns `true` if the given `$path` is relative
     *
     * @param string $path
     * @return boolean
     */
    static function isRelative($path) {
        return ! Paths::isAbsolute($path);
    }

    /**
     * Returns parent directory's path
     *
     * @param string $path
     * @return string the parent path, or `null` if the path is top-level
     */
    static function parent($path) {
        $drive = Paths::drive($path);
        $location = Paths::location($path);

        if ('/' === $location or '.' === $location) {
            return $drive;
        }

        if ($parent = dirname($location)) {

            if ('.' === $parent) {
                return $drive;
            }

            return Paths::join($drive, $parent);
        }
    }

    /**
     * Returns `true` if the `$child` path is relative to the `$parent` path
     *
     * Drive information is taken into account iff both paths contains them.
     *
     * Paths with different driver letters will yield `false`, without comparing
     * the actual file locations; so `isParentOf(C:/a, D:/a/b)` will be false but
     * both `isParentOf(C:/a, /a/b)` and `isParentOf(/a, D:/a/b)` will be true.
     *
     * @param  string $parent
     * @param  string $child
     * @return boolean
     */
    static function isParentOf($parent, $child) {

        $pd = Paths::drive($parent);
        $cd = Paths::drive($child);

        if ($pd and $cd and $pd !== $cd) {
            return false;
        }

        $pl = Paths::location($parent);
        $cl = Paths::location($child);

        return Strings::startsWith($cl, $pl);
    }

    /**
     * Concatenates the path fragments into a single path
     *
     * @example
     *    join(/a/b, /c) == /a/b/c
     *    join(/a/b, /c) == /a/b/c
     *    join(/a, b, /c) == /a/b/c
     *
     * @param  string $parent
     * @param  string ...$children
     * @return string
     */
    static function join($parent, $children) {

        if (!is_array($children)) {
            $children = Arrays::tail(func_get_args());
        }

        while (!$parent and count($children)) {
            $parent = array_shift($children);
        }

        foreach ($children as $child) {
            if ($child = trim($child)) {
                $parent = Paths::_join($parent, $child);
            }
        }

        return $parent;
    }

    private static function _join($parent, $child) {

        if (!$drive = Paths::drive($parent)) {
             $drive = Paths::drive($child);
        } elseif (Paths::drive($child) and $drive !== Paths::drive($child)) {
            return Paths::normalize($child);
        }

        $parent = Paths::location($parent);
        $child  = Paths::location($child);

        $parentEnds = Strings::endsWith($parent, '/');
        $childStarts = Strings::startsWith($child, '/');

        if ($parentEnds and $childStarts) {
            $child = substr($child, 1);
        } elseif (!($parentEnds or $childStarts)) {
            $parent .= '/';
        }

        return Paths::normalize($drive . $parent . $child);
    }

    /**
     * Returns a relative path for the given paths
     *
     * @param  string $parent
     * @param  string $child
     * @return string
     */
    static function relative($parent, $child) {
        Check::argument(
            Paths::isParentOf($parent, $child), "'%s' is not parent of '%s'", $parent, $child
        );

        $parent = Paths::location($parent);
        $child  = Paths::location($child);

        $relative = substr($child, strlen($parent));
        return $relative ? $relative : '/';
    }
}
