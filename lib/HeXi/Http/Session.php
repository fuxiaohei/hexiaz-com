<?php

/**
 *
 */
class Session {

    /**
     * @var Session
     */
    private static $self;

    /**
     * @return Session
     */
    public static function instance() {
        return !self::$self ? self::$self = new Session() : self::$self;
    }

    /**
     *
     */
    private function __construct() {
        session_start();
    }

    /**
     * @return Session
     */
    public static function start() {
        if (!session_id()) {
            session_start();
        }
        return self::$self;
    }

    /**
     * @return Session
     */
    public static function commit() {
        session_commit();
        return self::$self;
    }

    /**
     * @return Session
     */
    public static function destroy() {
        session_destroy();
        return self::$self;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public static function get($key) {
        return eval('return $_SESSION["' . str_replace('.', '"]["', $key) . '"];');
    }

    /**
     * @return array
     */
    public static function all() {
        return isset($_SESSION) ? $_SESSION : array();
    }

    /**
     * @param string $key
     * @param mixed  $value
     * @return mixed
     */
    public static function set($key, $value) {
        eval('$_SESSION["' . str_replace('.', '"]["', $key) . '"] = $value;');
        return $value;
    }

    /**
     * @param string $key
     * @return Session
     */
    public static function delete($key) {
        eval('unset($_SESSION["' . str_replace('.', '"]["', $key) . '"]);');
        return self::$self;
    }

    /**
     * @return Session
     */
    public static function clear() {
        $_SESSION = array();
        return self::$self;
    }

}
