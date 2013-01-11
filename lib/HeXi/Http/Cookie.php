<?php

/**
 *
 */
class Cookie {

    /**
     * @var Cookie
     */
    private static $self;

    /**
     * @return Cookie
     */
    public static function instance() {
        return !self::$self ? self::$self = new Cookie() : self::$self;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public static function get($key) {
        return $_COOKIE[$key];
    }

    /**
     * @param string   $key
     * @param string   $value
     * @param int      $expire
     * @param string   $path
     * @param string   $domain
     * @return Cookie
     */
    public static function set($key, $value, $expire, $path = '/', $domain = null) {
        setcookie($key, $value, NOW + $expire, $path, $domain);
        $_COOKIE[$key] = $value;
        return self::$self;
    }

    /**
     * @return mixed
     */
    public static function all() {
        return $_COOKIE;
    }

    /**
     * @param string $key
     * @return Cookie
     */
    public static function remove($key) {
        setcookie($key, null, NOW - 3600);
        unset($_COOKIE[$key]);
        return self::$self;
    }

    /**
     * @return Cookie
     */
    public static function clear() {
        $_COOKIE = array();
        return self::$self;
    }

    /**
     * @param string $key
     * @param string $value
     * @param string $path
     * @param string $domain
     * @return Cookie
     */
    public static function forever($key, $value, $path = '/', $domain = null) {
        self::set($key, $value, 10 * 365 * 24 * 3600, $path, $domain);
        return self::$self;
    }
}
