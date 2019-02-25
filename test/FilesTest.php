<?php

namespace Prelude;

class FilesTest extends \PHPUnit_Framework_TestCase {

    function test_path() {
        $this->assertEquals(__DIR__, Files::path(__FILE__));
        $this->assertEquals(dirname(__DIR__), Files::path(__DIR__));
    }

    function test_name() {
        $list = array(
            'a'      => 'a',
            'a.b'    => 'a',
            'a.b.c'  => 'a.b',
        #   hidden
            '.a'     => '.a',
            '.a.b'   => '.a',
            '.a.b.c' => '.a.b'
        );

        forEach ($list as $file => $name) {
            $this->assertEquals($name, Files::name($file), "Files::name($file)");
        }
    }

    /**
     * @dataProvider provider_type
     */
    function test_type($file, $type) {
        $this->assertEquals($type, Files::type($file), "Files::type($file)");
    }

    function provider_type() {
        # format: file, type
        return array(
            array('a'     , null),
            array('a.b'   , 'b'),
            array('a.b.c' , 'c'),
        #   hidden
            array('.a'    , null),
            array('.a.b'  , 'b'),
            array('.a.b.c', 'c'),
        );
    }

    /**
     * @dataProvider provider_mime
     */
    function test_mime($mime, $file) {
        $this->assertEquals($mime , Files::mime($file), "Files::mime($file)");
    }

    function provider_mime() {
        $root = realpath(__DIR__ . '/..');

        # format: mime, file
        return array(
            array('directory'       , __DIR__),
        //  array('text/x-php'      , __FILE__), travis fails to recognize as a php file
            array('text/plain'      , "$root/README.md"),
            array('application/xml' , "$root/phpunit.xml"),
            array('text/plain'      , "$root/composer.json"),
        );
    }

    function test_defaultType() {
        $this->assertEquals('/dir/index.html', Files::defaultType('/dir/index',      'html'));
        $this->assertEquals('/dir/index.html', Files::defaultType('/dir/index.html', 'html'));

        $this->assertEquals('/dir/jquery.js'    , Files::defaultType('/dir/jquery',        'js'));
        $this->assertEquals('/dir/jquery.min.js', Files::defaultType('/dir/jquery.min',    'js'));
        $this->assertEquals('/dir/jquery.min.js', Files::defaultType('/dir/jquery.min.js', 'js'));
    }

    function test_withType() {
        $tests = array(
                   'index.css' => array(       'index',        'index.less',        'index.css'),
                  '/index.css' => array(      '/index',       '/index.less',       '/index.css'),
               'dir/index.css' => array(   'dir/index',    'dir/index.less',    'dir/index.css'),
              '/dir/index.css' => array(  '/dir/index',   '/dir/index.less',   '/dir/index.css'),
              '/dir/index.css' => array(  '/dir/index',   '/dir/index.less',   '/dir/index.css'),
            'C:/dir/index.css' => array('C:/dir/index', 'C:/dir/index.less', 'C:/dir/index.css'),
            # win paths
            'D:/path/file.css' => array(
                'D:\\path\\file',
                'D:\\path\\file.less',
                'D:\\path\\file.css'
            )

        );

        forEach ($tests as $expected => $list)
            forEach ($list as $test) {
                $this->assertEquals($expected, Files::withType($test,  'css'), "Files::withType($test, 'css')" );
                $this->assertEquals($expected, Files::withType($test, '.css'), "Files::withType($test, '.css')");
            }

        # multiple extensions
        $this->assertEquals('jquery.min.js', Files::withType('jquery',  'min.js'));
        $this->assertEquals('jquery.min.js', Files::withType('jquery', '.min.js'));
    }
}
