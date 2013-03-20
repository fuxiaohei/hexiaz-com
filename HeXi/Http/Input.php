<?php

/**
 * 输入数据处理
 * Class Input
 */
class Input {

    /**
     * 获取数据
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public static function get($name, $default = null) {
        if (isset($_GET[$name])) {
            return $_GET[$name];
        }
        if (isset($_POST[$name])) {
            return $_POST[$name];
        }
        if (isset($_COOKIE[$name])) {
            return $_COOKIE[$name];
        }
        return $default;
    }

    /**
     * 组合数据成为数组
     * @param array $names
     * @return array
     */
    public static function assume(array $names) {
        $tmp = array();
        foreach ($names as $n) {
            $tmp[$n] = self::get($n);
        }
        return $tmp;
    }

    /**
     * 操作Cookie数据
     * @param string $name
     * @param mixed $value
     * @param int $expire
     * @param string $path
     * @param null|string $domain
     * @return mixed
     */
    public static function cookie($name, $value = null, $expire = 0, $path = '/', $domain = null) {
        if ($value === null) {
            return $_COOKIE[$name];
        }
        setcookie($name, $value, time() + $expire, $path, $domain);
        $_COOKIE[$name] = $value;
    }

    /**
     * 操作Session数据
     * @param string $name
     * @param mixed $value
     * @return bool|mixed
     */
    public static function session($name, $value = null) {
        #开启Session
        if (strtoupper($name) === 'START') {
            session_cache_limiter(0);
            session_start();
            return true;
        }
        #提交COMMIT
        if (strtoupper($name)  === 'COMMIT') {
            session_commit();
            return true;
        }
        #销毁SESSION
        if (strtoupper($name)  === 'DESTROY') {
            session_destroy();
            return true;
        }
        if (!session_id()) {
            return self::session('START');
        }
        #返回值
        if ($value === null) {
            return $_SESSION[$name];
        }
        #设置值
        $_SESSION[$name] = $value;
    }
}