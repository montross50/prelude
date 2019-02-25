<?php

namespace Prelude;

class PathsTest extends \PHPUnit_Framework_TestCase {

    private $absolutes = array('/', '/a', '/a/b', 'C:', 'C:/');
    private $relatives = array('', '.', '..', 'a', 'a/b', './a/b', '../', '../a');

    /**
     *
     * @param  string $expected
     * @param  string $path
     * @dataProvider provider_normalize
     */
    function test_normalize($normalized, $path) {
        $this->assertEquals($normalized, Paths::normalize($path));
    }

    function provider_normalize() {
        # format: array(normalized, path)
        return array(
            array('/a/b', '/a/b'),
            array('/a/b', '\\a\\b'),
            array('/a/b', '\\a/b'),
            array('/a/b', '/a\\b'),
            # win
            array('A:/', 'A:'),
            array('B:/', 'B:/'),
            array('C:/', 'C:\\'),
        );
    }

    function test_parse_with_absolute() {
        $this->assertEquals(array('dir' => '/',    'file' => 'c.e'), Paths::parse('/c.e'    ));
        $this->assertEquals(array('dir' => '/a',   'file' => 'c.e'), Paths::parse('/a/c.e'  ));
        $this->assertEquals(array('dir' => '/a/b', 'file' => 'c.e'), Paths::parse('/a/b/c.e'));

        $this->assertEquals(array('dir' => '/',    'file' => 'c'  ), Paths::parse('/c'    ));
        $this->assertEquals(array('dir' => '/a',   'file' => 'c'  ), Paths::parse('/a/c'  ));
        $this->assertEquals(array('dir' => '/a/b', 'file' => 'c'  ), Paths::parse('/a/b/c'));
    }

    function test_parse_with_absolute_win_drive() {
        $drive = array(
            'drive' => 'C:',
            'dir'   => '/',
            'file'  => null
        );
        $this->assertEquals($drive, Paths::parse('C:'));
        $this->assertEquals($drive, Paths::parse('C:/'));
        $this->assertEquals($drive, Paths::parse('C:\\'));
    }

    function test_parse_with_absolute_win_dir() {
        $dir = array(
            'drive' => 'C:',
            'dir'   => '/',
            'file'  => 'dir'
        );
        $this->assertEquals($dir, Paths::parse('C:/dir'));
        $this->assertEquals($dir, Paths::parse('C:/dir/'));
        $this->assertEquals($dir, Paths::parse('C:\\dir'));
        $this->assertEquals($dir, Paths::parse('C:\\dir\\'));
    }

    function test_parse_with_absolute_win_file() {
        $file = array(
            'drive' => 'C:',
            'dir'   => '/dir',
            'file'  => 'file'
        );
        $this->assertEquals($file, Paths::parse('C:/dir/file'));
        $this->assertEquals($file, Paths::parse('C:/dir/file/'));
        $this->assertEquals($file, Paths::parse('C:\\dir\\file'));
        $this->assertEquals($file, Paths::parse('C:\\dir\\file\\'));
    }

    function test_parse_with_relative() {
        $this->assertEquals(array('dir' => null ,  'file' => 'c.e'), Paths::parse(    'c.e'));
        $this->assertEquals(array('dir' =>   'a',  'file' => 'c.e'), Paths::parse(  'a/c.e'));
        $this->assertEquals(array('dir' => 'b/a',  'file' => 'c.e'), Paths::parse('b/a/c.e'));
    }

    function test_drive() {
        $this->assertNull(Paths::drive(''));
        $this->assertNull(Paths::drive('/'));
        $this->assertNull(Paths::drive('/a/b'));

        $this->assertEquals('C:', Paths::drive('C:'));
        $this->assertEquals('C:', Paths::drive('C:/'));
        $this->assertEquals('C:', Paths::drive('C:\\'));
        $this->assertEquals('C:', Paths::drive('C:/a/b'));
        $this->assertEquals('C:', Paths::drive('C:\\a\\b'));
    }

    function test_dir() {
        # absolutes
        $this->assertEquals('/' , Paths::dir('/'));
        $this->assertEquals('/' , Paths::dir('/a'));
        $this->assertEquals('/' , Paths::dir('/a/'));
        $this->assertEquals('/a', Paths::dir('/a/b'));
        # relatives
        $this->assertEquals('' , Paths::dir(''));
        $this->assertEquals('' , Paths::dir('a'));
        $this->assertEquals('' , Paths::dir('a/'));
        $this->assertEquals('a', Paths::dir('a/b'));
    }

    function test_file() {
        # absolutes
        $this->assertEquals('' , Paths::file('/'));
        $this->assertEquals('a', Paths::file('/a'));
        $this->assertEquals('a', Paths::file('/a/'));
        $this->assertEquals('b', Paths::file('/a/b'));
        # relatives
        $this->assertEquals('' , Paths::file(''));
        $this->assertEquals('a', Paths::file('a'));
        $this->assertEquals('a', Paths::file('a/'));
        $this->assertEquals('b', Paths::file('a/b'));
    }

    /**
     * @dataProvider provider_location
     */
    function test_location($path, $location) {
        $this->assertEquals($location, Paths::location($path));
    }

    function provider_location() {
        return array(
            # absolutes
            array('/'   , '/'   ),
            array('/a'  , '/a'  ),
            array('/a/' , '/a'  ),
            array('/a/b', '/a/b'),

            # relatives
            array(''   , ''   ),
            array('a'  , 'a'  ),
            array('a/' , 'a'  ),
            array('a/b', 'a/b'),

            # win
            array('F:'    , '/'   ),
            array('F:/'   , '/'   ),
            array('F:/a'  , '/a'  ),
            array('F:/a/' , '/a'  ),
            array('F:/a/b', '/a/b'),
        );
    }

     function test_base() {
        # absolutes
        $this->assertEquals('/' , Paths::base('/'   ));
        $this->assertEquals('/' , Paths::base('/a'  ));
        $this->assertEquals('/' , Paths::base('/a/' ));
        $this->assertEquals('/a', Paths::base('/a/b'));
        # relatives
        $this->assertEquals( '' , Paths::base( ''   ));
        $this->assertEquals( '' , Paths::base( 'a'  ));
        $this->assertEquals( '' , Paths::base( 'a/' ));
        $this->assertEquals( 'a', Paths::base( 'a/b'));
        # win
        $this->assertEquals('F:/' , Paths::base('F:'    ));
        $this->assertEquals('F:/' , Paths::base('F:/'   ));
        $this->assertEquals('F:/' , Paths::base('F:/a'  ));
        $this->assertEquals('F:/' , Paths::base('F:/a/' ));
        $this->assertEquals('F:/a', Paths::base('F:/a/b'));
    }

    function test_isAbsolute() {
        forEach($this->absolutes as $test)
            $this->assertTrue (Paths::isAbsolute($test), "Paths::isAbsolute($test)");

        forEach($this->relatives as $test)
            $this->assertFalse(Paths::isAbsolute($test), "Paths::isAbsolute($test)");
    }


    function test_isRelative() {
        forEach($this->absolutes as $test)
            $this->assertFalse(Paths::isRelative($test), "Paths::isRelative($test)");

        forEach($this->relatives as $test)
            $this->assertTrue (Paths::isRelative($test), "Paths::isRelative($test)");
    }

    /**
     * @dataProvider provider_parent
     */
    function test_parent($parent, $child) {
        $this->assertEquals($parent, Paths::parent($child));
    }

    function provider_parent() {
        return array(
            # *nix
            array(null, ''  ),
            array(null, '/' ),
            array(null,  'a'),
            array('/' , '/a'),

            # win*
            array('A:', 'A:/'),
            array('B:', 'B:'),
            array('C:/' , 'C:/a'),
        );
    }

    function test_isParentOf() {
        $this->assertTrue (Paths::isParentOf(  '/',    '/x'));
        $this->assertTrue (Paths::isParentOf('C:/',    '/x'));
        $this->assertTrue (Paths::isParentOf('C:/',  'C:/x'));
        $this->assertFalse(Paths::isParentOf('C:/',  'D:/x'));
        $this->assertTrue (Paths::isParentOf(  '/',  'D:/x'));

        $this->assertFalse(Paths::isParentOf(  '/a',    '/'));
        $this->assertTrue (Paths::isParentOf(  '/' ,    '/a'));
        $this->assertTrue (Paths::isParentOf('C:/a',    '/a/b'));
        $this->assertFalse(Paths::isParentOf('C:/a',  'C:/b'));
        $this->assertTrue (Paths::isParentOf('C:/a',  'C:/a/b'));
        $this->assertFalse(Paths::isParentOf('C:/a',  'D:/a/b'));
        $this->assertTrue (Paths::isParentOf(  '/a',  'D:/a'));
    }

    /**
     * @param string $result
     * @param string $path1
     * @param string $path2
     *
     * @dataProvider provider_join
     */
    function test_join($joined, $path1, $path2, $path3=null) {
        $this->assertEquals($joined, Paths::join($path1, $path2, $path3));
    }

    function provider_join() {
        return array(
            array('/'   , ''   , '/' ),
            array('/'   , '/'  , ''  ),
            array('/'   , '/'  , '/' ),
            array('/a'  , '/'  ,  'a'),
            array('/a'  , '/'  , '/a'),
            array( 'a'  ,  'a' , '/' ),
            array( 'a/a',  'a' ,  'a'),
            array( 'a/a',  'a' , '/a'),
            array('/a/a', '/a' ,  'a'),
            array('/a/a', '/a' , '/a'),
            array('/a/a', '/a' ,  'a'),
            array('/a/a', '/a/',  'a'),
            array('/a/b', '/a' , '/b'),
            array('/a/b', '/a/', '/b'),

            # win-like unix-dash
            array('C:/'   , 'C:'  ,   '/' ),
            array('C:/0'  , 'C:'  ,   '/0'),
            array('C:/1'  , 'C:'  ,    '1'),
            array('C:/a'  , 'C:'  ,   '/a'),
            array('C:/a'  , 'C:/' ,   '/a'),
            array('C:/1'  , 'C:/'  ,   '1'),
            array('D:/a/b',   '/a', 'D:/b'),
            array('D:/b'  , 'C:/a', 'D:/b'),
            array('D:/b'  , 'C:/b', 'D:/b'),

            # win-like win-dash
            array('C:/0'  , 'C:'   ,   '\\0'),
            array('C:/1'  , 'C:'   ,     '1'),
            array('C:/a'  , 'C:'   ,   '\\a'),
            array('C:/a'  , 'C:\\' ,   '\\a'),
            array('C:/1'  , 'C:\\' ,     '1'),
            array('D:/a/b',   '\\a', 'D:\\b'),
            array('D:/b'  , 'C:\\a', 'D:\\b'),
            array('D:/b'  , 'C:\\b', 'D:\\b'),

            # multiple
            array('/a/b/c', '/a', '/b', '/c'),
            array('/a/b/c', '/a', '/b',  'c'),
            array('/a/b/c', '/a',  'b', '/c'),
        );
    }

    function test_relative() {
        $this->assertEquals('/', Paths::relative('/' , '/'));
        $this->assertEquals('/', Paths::relative('/a', '/a'));
        $this->assertEquals('/', Paths::relative('/a', '/a/'));

        $this->assertEquals('/', Paths::relative('C:/' , '/'));
        $this->assertEquals('/', Paths::relative('C:/a', '/a'));
        $this->assertEquals('/', Paths::relative('C:/a', '/a/'));

        $this->assertEquals('/', Paths::relative('/' , 'C:/'));
        $this->assertEquals('/', Paths::relative('/a', 'C:/a'));
        $this->assertEquals('/', Paths::relative('/a', 'C:/a/'));

        $this->assertEquals('/', Paths::relative('C:/' , 'C:/'));
        $this->assertEquals('/', Paths::relative('C:/a', 'C:/a'));
        $this->assertEquals('/', Paths::relative('C:/a', 'C:/a/'));

        $this->assertEquals('/rel', Paths::relative( 'dir' , 'dir/rel'));
        $this->assertEquals('/rel', Paths::relative('/dir', '/dir/rel'));
    }
}

