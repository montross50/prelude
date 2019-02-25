<?php

namespace Prelude;

/**
 * url = scheme + host + path + query + fragment
 *       {    base   } + path + {    params    }
 */
final class Urls {

    /**
     * @param string $url
     * @param string|int $fragment
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    static function extract($url, $fragment) {

        Check::argument(is_string($url), 'invalid url given');

        $FRAGMENTS = array(
            'scheme'   => PHP_URL_SCHEME,
            'host'     => PHP_URL_HOST,
            'hostname' => PHP_URL_HOST,
            'port'     => PHP_URL_PORT,
            'user'     => PHP_URL_USER,
            'username' => PHP_URL_USER,
            'pass'     => PHP_URL_PASS,
            'password' => PHP_URL_PASS,
            'path'     => PHP_URL_PATH,
            'query'    => PHP_URL_QUERY,
            'fragment' => PHP_URL_FRAGMENT,
        );

        if (is_int($fragment)) {
            Check::argument(Arrays::contains($FRAGMENTS, $fragment), 'unknown url fragment');
            $const = $fragment;
        } elseif (is_string($fragment)) {
            $const = Arrays::getOrThrow($FRAGMENTS, $fragment, new \InvalidArgumentException("unknown url fragment $fragment"));
        } else {
            throw new \InvalidArgumentException();
        }

        if (Urls::isImplicit($url)) {
            if ($const === PHP_URL_SCHEME) {
                return null;
            }
            $url = "http:$url";
        }

        return parse_url($url, $const);
    }

    /**
     *
     * @param  string $url
     * @return array {
     *   [scheme] => http
     *   [host] => hostname
     *   [user] => username
     *   [pass] => password
     *   [path] => /path
     *   [query] => arg=value
     *   [fragment] => anchor
     * }
     *
     * @throws \InvalidArgumentException if an invalid `$url` if given
     */
    static function parse($url) {

        if ($isImplicit = Urls::isImplicit($url)) {
            $url = "http:$url";
        }

        if ($pieces = parse_url($url)) {
            if ($isImplicit) {
                unset($pieces['scheme']);
            }
            return $pieces;
        }

        throw new \InvalidArgumentException("invalid url: $url");
    }

    /**
     * @example
     *    base(http://t.co/path/file.html) == 'http://t.co'
     *    base(     //t.co/path/file.html) ==      '//t.co'
     *    base(           /path/file.html) == ''
     *
     * @param string $url
     * @return string
     */
    static function base($url) {

        if ($scheme = Urls::scheme($url)) {
            $scheme .= ':';
        }

        if ($host = Urls::host($url)) {
            return "$scheme//$host";
        }

        return null;
    }

    /**
     * @param string $url
     * @return string
     */
    static function scheme($url) {
        return Urls::extract($url, PHP_URL_SCHEME);
    }

    /**
     * @param string $url
     * @return string
     */
    static function host($url) {
        return Urls::extract($url, PHP_URL_HOST);
    }

    /**
     * @param string $url
     * @return string
     */
    static function port($url) {
        return Urls::extract($url, PHP_URL_PORT);
    }

    /**
     * @param string $url
     * @return string
     */
    static function user($url) {
        return Urls::extract($url, PHP_URL_USER);
    }

    /**
     * @param string $url
     * @return string
     */
    static function pass($url) {
        return Urls::extract($url, PHP_URL_PASS);
    }

    /**
     * @param string $url
     * @return string
     */
    static function path($url) {
        return Urls::extract($url, PHP_URL_PATH);
    }

    /**
     * @param string $url
     * @return string
     */
    static function query($url) {
        return Urls::extract($url, PHP_URL_QUERY);
    }

    /**
     * @param string $url
     * @return string
     */
    static function fragment($url) {
        return Urls::extract($url, PHP_URL_FRAGMENT);
    }

    /**
     * @param string $url
     * @return string
     */
    static function tail($url) {
        $buffer = array();

        if ($query = Urls::query($url)) {
            $buffer[] = '?';
            $buffer[] = $query;
        }

        if ($fragment = Urls::fragment($url)) {
            $buffer[] = '#';
            $buffer[] = $fragment;
        }

        return implode('', $buffer);
    }

    /**
     * @param string $url
     * @return boolean
     */
    static function isImplicit($url) {
        Check::argument(is_string($url), 'invalid url');
        return $url and Strings::startsWith($url, '//');
    }

    /**
     * @param string $url
     * @param string $hostname [optional]
     *
     * @return boolean
     */
    static function isExternal($url, $hostname = null) {

        $host = Urls::host($url);

        if ($hostname) {
            return !$host or $host !== $hostname;
        }

        return (boolean) $host;
    }

    /**
     * @param  string $url
     * @return boolean
     */
    static function isAbsolute($url) {
        return $url and '/' === $url[0] or Urls::isExternal($url);
    }

    /**
     * @param  string $url
     * @return boolean
     */
    static function isRelative($url) {
        return ! Urls::isAbsolute($url);
    }

    /**
     *
     * @example
     *    join(http://t.co/post/comments/, /index.html) == http://t.co/index.html
     *    join(/post/comments/,  index.html) == /post/comments/index.html
     *
     *    external urls
     *    join(http://t.co/post, http://fb.com/friends) == http://fb.com/friends
     *
     * @param string $rootUrl
     * @param string $url
     *
     * @return string
     */
    static function join($rootUrl, $url) {

        if (Urls::isExternal($url)) {
            return $url;
        }

        return Strings::concat(
             Urls::base($rootUrl),
            Paths::join(Urls::path($rootUrl), Urls::path($url)),
             Urls::tail($url)
        );
    }
}
