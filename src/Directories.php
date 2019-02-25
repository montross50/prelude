<?php

namespace Prelude;

final class Directories {

    /**
     * @deprecated please use Directories::children
     */
    static function childs($dir) {
        return Directories::children($dir);
    }

    /**
     * @param string $dir
     * @return array
     */
    static function children($dir) {

        if (is_file($dir)) {
            $dir = Paths::base($dir);
        }

        $children = array();

        /**
         * @var \DirectoryIterator $file
         */
        foreach (new \DirectoryIterator($dir) as $file) {

            if ($file->isDot()) continue;
            $children[] = $file->getPathName();
        }

        return $children;
    }

    /**
     * @param string $dir
     * @return array
     */
    static function subdirs($dir) {
        return array_filter(Directories::children($dir), 'is_dir');
    }

    /**
     * @param string $dir
     * @return array
     */
    static function files($dir) {
        return array_filter(Directories::children($dir), 'is_file');
    }
}
