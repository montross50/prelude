<?php

namespace Prelude;

final class Files {

    /**
     *
     */
    static function path($file) {
        return Paths::base($file);
    }

    /**
     * Examples:
     *     name('/dir/index.html') == 'index'
     *     name('/dir/jquery.min.js') == 'jquery.min'
     *
     * @param  string $file
     * @return string
     */
    static function name($file) {
        $file = Paths::file($file);
        $index = Strings::lastIndexOf($file, '.');
        if ($index > 0) {
            return substr($file, 0, $index);
        }
        return $file;
    }

    /**
     * Returns the file's extension type
     *
     * Examples:
     *     type('/dir/index.html') == 'html'
     *     type('/dir/jquery.min.js') == 'js'
     *
     * @param string $file
     * @return string
     */
    static function type($file) {
        $file = Paths::file($file);
        $index = Strings::lastIndexOf($file, '.');
        if ($index > 0) {
            return substr($file, $index + 1);
        }
    }

    /**
     * Returns the file's mime content type
     *
     * @param string $file
     * @param string $magicFile [optional]
     * @return string
     */
    static function mime($file, $magicFile = null) {
        if (class_exists('FInfo')) {
            $fi = new \FInfo(FILEINFO_MIME_TYPE, $magicFile);
            return $fi->file($file);
        }
        // @codeCoverageIgnoreStart
        return mime_content_type($file);
        // @codeCoverageIgnoreEnd
    }

    /**
     * Examples:
     *     defaultType('/dir/index',         'html') == '/dir/index.html'
     *     defaultType('/dir/index.html',    'hmtl') == '/dir/index.html'
     *
     *     defaultType('/dir/jquery',        'js'  ) == '/dir/jquery.js'
     *     defaultType('/dir/jquery.min',    'js'  ) == '/dir/jquery.min.js'
     *     defaultType('/dir/jquery.min.js', 'js'  ) == '/dir/jquery.min.js'
     *
     * @param  string $file
     * @param  string $type
     * @return string
     */
    static function defaultType($file, $type) {
        return $type === Files::type($file) ? $file : "$file.$type";
    }

    /**
     * Examples:
     *     withType('/dir/index'     , 'css') == '/dir/index.css'
     *     withType('/dir/index.less', 'css') == '/dir/index.css'
     *     withType('/dir/index.css' , 'css') == '/dir/index.css'
     *
     * @param  string $file
     * @param  string $type
     * @return string
     */
    static function withType($file, $type) {

        if ('.' === $type[0]) {
            $type = substr($type, 1);
        }

        $fileName = Files::name($file) . '.' . $type;

        if ($base = Paths::base($file)) {
            return Paths::join($base, $fileName);
        }

        return $fileName;
    }
}
