<?php

namespace Prelude;

class UrlsTest extends \PHPUnit_Framework_TestCase {

    function externalUrls() {
        return array(
            array('https://github.com/eridal/prelude'),
            array('ftp://test.com/'),
            array('//example.org'),
        );
    }

    function nonExternalUrls() {
        return array_merge($this->absolutePaths(),
                           $this->relativePaths());
    }

    function externalUrlsHosts() {
        $data = $this->externalUrls();

        foreach ($data as $i => $url) {
            $data[$i][] = Urls::host($url[0]);
        }

        return $data;
    }

    function nonExternalUrlsHosts() {
        $data = null;
        foreach ($this->nonExternalUrls() as $urls) {
            $data[] = Arrays::merge($urls, 't.co');
        }
        return $data;
    }

    function absolutePaths() {
        return array(
            array('/'),
            array('/simple'),
            array('/directory/'),
            array('/dir/index.html'),
        );
    }

    function relativePaths() {
        return array(
            array(''),
            array('eridal/prelude'),
            array('?query'),
            array('#fragment'),
        );
    }

    function absoluteUrls() {
        return array_merge($this->externalUrls(),
                           $this->absolutePaths());
    }

    function nonUrls() {
        return array(
            array(null),
            array(false),
            array(true),
            array(1),
            array(1.23),
            array(array()),
            array($this),
        );
    }

    /**
     * @dataProvider absoluteUrls
     */
    function testIsAbsolute_withAbsoluteUrl($url) {
        $this->assertTrue(Urls::isAbsolute($url));
    }

    /**
     * @dataProvider relativePaths
     */
    function testIsAbsolute_withRelativeUrl($url) {
        $this->assertFalse(Urls::isAbsolute($url));
    }

    /**
     * @dataProvider relativePaths
     */
    function testIsRelative_withRelativeUrl($url) {
        $this->assertTrue(Urls::isRelative($url));
    }

    /**
     * @dataProvider absoluteUrls
     */
    function testIsRelative_withAbsoluteUrl($url) {
        $this->assertFalse(Urls::isRelative($url));
    }

    /**
     * @dataProvider externalUrls
     */
    function testIsExternal_withExternalUrl($url) {
        $this->assertTrue(Urls::isExternal($url));
    }

    /**
     * @dataProvider nonExternalUrls
     */
    function testIsExternal_withNonExternalUrl($url) {
        $this->assertFalse(Urls::isExternal($url));
    }

    /**
     * @dataProvider externalUrlsHosts
     */
    function testIsExternal_withExternalUrl_withHost($url, $host) {
        $this->assertFalse(Urls::isExternal($url, $host));
    }

    /**
     * @dataProvider nonExternalUrlsHosts
     */
    function testIsExternal_withNonExternalUrl_withHost($url, $host) {
        $this->assertTrue(Urls::isExternal($url, $host));
    }

    /**
     * @dataProvider urls
     */
    function testParse($url, array $pieces) {
        $this->assertEquals($pieces, Urls::parse($url));
    }

    /**
     * @dataProvider urls
     */
    function testScheme($url, array $pieces) {
        $this->assertEquals(Arrays::get($pieces, 'scheme'), Urls::scheme($url));
    }

    /**
     * @dataProvider urls
     */
    function testHost($url, array $pieces) {
        $this->assertEquals(Arrays::get($pieces, 'host'), Urls::host($url));
    }

    /**
     * @dataProvider urls
     */
    function testPort($url, array $pieces) {
        $this->assertEquals(Arrays::get($pieces, 'port'), Urls::port($url));
    }

    /**
     * @dataProvider urls
     */
    function testUser($url, array $pieces) {
        $this->assertEquals(Arrays::get($pieces, 'user'), Urls::user($url));
    }

    /**
     * @dataProvider urls
     */
    function testPass($url, array $pieces) {
        $this->assertEquals(Arrays::get($pieces, 'pass'), Urls::pass($url));
    }

    /**
     * @dataProvider urls
     */
    function testPath($url, array $pieces) {
        $this->assertEquals(Arrays::get($pieces, 'path'), Urls::path($url));
    }

    /**
     * @dataProvider urls
     */
    function testQuery($url, array $pieces) {
        $this->assertEquals(Arrays::get($pieces, 'query'), Urls::query($url));
    }

    /**
     * @dataProvider urls
     */
    function testFragment($url, array $pieces) {
        $this->assertEquals(Arrays::get($pieces, 'fragment'), Urls::fragment($url));
    }

    function urls() {

        $scheme = 'sftp';
        $host = 't.co';
        $path = '/abc';
        $query = '?a=b&c=d';
        $fragment = '#fragment';


        $has_no_path = array('path' => '');

        $has_scheme   = array('scheme'   => $scheme);
        $has_host     = array('host'     => $host  );
        $has_path     = array('path'     => $path  );
        $has_query    = array('query'    => substr($query   , 1));
        $has_fragment = array('fragment' => substr($fragment, 1));

        return array(

            array(''       , $has_no_path),
            array($query   , $has_query),
            array($fragment, $has_fragment),

            array($path                     , $has_path),
            array($path . $query            , $has_path + $has_query),
            array($path          . $fragment, $has_path + $has_fragment),
            array($path . $query . $fragment, $has_path + $has_query + $has_fragment),

            array(    "//{$host}{$path}"                   , $has_host + $has_path),
            array(    "//{$host}{$path}{$query}"           , $has_host + $has_path + $has_query),
            array(    "//{$host}{$path}{$fragment}"        , $has_host + $has_path + $has_fragment),
            array(    "//{$host}{$path}{$query}{$fragment}", $has_host + $has_path + $has_query + $has_fragment),

            array("{$scheme}://{$host}{$path}"                   , $has_scheme + $has_host + $has_path),
            array("{$scheme}://{$host}{$path}{$query}"           , $has_scheme + $has_host + $has_path + $has_query),
            array("{$scheme}://{$host}{$path}{$fragment}"        , $has_scheme + $has_host + $has_path + $has_fragment),
            array("{$scheme}://{$host}{$path}{$query}{$fragment}", $has_scheme + $has_host + $has_path + $has_query + $has_fragment),

        );
    }

    function components() {
        return array(
            array(PHP_URL_SCHEME),
            array(PHP_URL_HOST),
            array(PHP_URL_PORT),
            array(PHP_URL_USER),
            array(PHP_URL_PASS),
            array(PHP_URL_QUERY),
            array(PHP_URL_PATH),
            array(PHP_URL_FRAGMENT),
            array('scheme'),
            array('host'),
            array('hostname'),
            array('port'),
            array('user'),
            array('username'),
            array('pass'),
            array('password'),
            array('path'),
            array('query'),
            array('fragment'),
        );
    }

    function nonComponents() {
        return array(
            array($this),
            array(null),
            array('ftp://s.co/'),
            array(false),
            array(true),
            array(12.23),
            array('test'),
        );
    }

    function  providerExtract_withbadUrls() {
        $data = array();

        foreach ($this->components() as $component) {
            foreach ($this->badUrls() as $url) {
                $data[] = Arrays::merge($component, $url);
            }
        }
        return $data;
    }


    /**
     * @dataProvider providerExtract_withbadUrls
     */
    function testExtract_withBadUrl($component, $url) {
        $this->assertNull(Urls::extract($url, $component));
    }

    /**
     * @dataProvider nonComponents
     * @expectedException \InvalidArgumentException
     */
    function testExtract_withBadComponents($url) {
        Urls::extract('http://t.co/index.html', $url);
        $this->fail();
    }

    /**
     * @dataProvider nonUrls
     * @expectedException \InvalidArgumentException
     */
    function testExtract_withNonUrls($url) {
        Urls::extract($url, PHP_URL_PATH);
        $this->fail();
    }

    /**
     * @dataProvider badUrls
     * @expectedException \InvalidArgumentException
     */
    function testParse_withBadUrl($url) {
        Urls::parse($url);
        $this->fail();
    }

    function badUrls() {
        return array(
            array('?'),
            array('#'),
        );
    }

    /**
     * @dataProvider providerBase
     */
    function testBase($url, $base) {
        $this->assertEquals($base, Urls::base($url));
    }

    function providerBase() {

        return array_map(function ($args) {
            if ($scheme = Arrays::get($args[1], 'scheme')) {
                $args[1] = $scheme . '://' . Arrays::getOrThrow($args[1], 'host');
            } elseif($host = Arrays::get($args[1], 'host')) {
                $args[1] = '//' . $host;
            } else {
                $args[1] = '';
            }
            return $args;
        }, $this->urls());
    }

    /**
     * @dataProvider providerTail
     */
    function testTail($url, $tail) {
        $this->assertEquals($tail, Urls::tail($url));
    }

    function providerTail() {
        return array_map(function($args) {

            $tail = '';

            if ($query = Arrays::get($args[1], 'query')) {
                $tail .= '?' . $query;
            }

            if ($fragment = Arrays::get($args[1], 'fragment')) {
                $tail .= '#' . $fragment;
            }

            $args[1] = $tail;
            return $args;
        }, $this->urls());
    }

    function test_join_defaults() {
        $this->assertEquals('/',     Urls::join('', '/'));
        $this->assertEquals('/',     Urls::join('/', ''));
        $this->assertEquals('/',     Urls::join('/', '/'));
        $this->assertEquals('/a',    Urls::join('/', 'a'));

        $this->assertEquals('#test', Urls::join('', '#test'));
    }

    function test_join_absolutes() {
        $this->assertEquals(    '//test.com/abc/cda', Urls::join(    '//test.com/abc' ,               '/cda'));
        $this->assertEquals('ftp://test.com/abc/cda', Urls::join('ftp://test.com/abc' ,                'cda'));
        $this->assertEquals('ftp://test.com/abc/cda', Urls::join('ftp://test.com/abc/',                'cda'));
        $this->assertEquals(    '//test.com/cda',     Urls::join(              '/abc' ,     '//test.com/cda'));
        $this->assertEquals('ftp://test.com/cda',     Urls::join(    '//test.com/abc/', 'ftp://test.com/cda'));
    }
}

