<?php

/**
 * 请求类
 * Class Request
 */
class Request {

    /**
     * 获取IP地址
     * @return string
     */
    public static function ip() {
        if ($_SERVER['HTTP_CLIENT_IP']) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }
        if ($_SERVER['HTTP_X_FORWARDED_FOR']) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        if ($_SERVER['REMOTE_ADDR']) {
            return $_SERVER['REMOTE_ADDR'];
        }
        return 'unknown';
    }

    /**
     * 获取用户标识
     * @return string
     */
    public static function userAgent() {
        return $_SERVER['HTTP_USER_AGENT'];
    }

    /**
     * 获取主机名称
     * @return string
     */
    public static function host() {
        if ($_SERVER['SERVER_NAME']) {
            return $_SERVER['SERVER_NAME'];
        }
        return $_SERVER['HTTP_HOST'];
    }

    /**
     * 获取基本URL地址
     * @return string
     */
    public static function baseUrl() {
        $protocol = $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://';
        return $protocol . self::host() . '/';
    }

    /**
     * 获取请求URL地址
     * @return mixed
     */
    public static function requestUrl() {
        return $_SERVER['REQUEST_URI'];
    }

    /**
     * 获取请求时间
     * @return mixed
     */
    public static function requestTime() {
        return $_SERVER['REQUEST_TIME'];
    }

    /**
     * 获取HTTP头信息
     * @param string $name
     * @return mixed
     */
    public static function http($name) {
        return $_SERVER['HTTP_' . str_replace('-', '_', strtoupper($name))];
    }

    /**
     * 获取SERVER信息
     * @param string $name
     * @return mixed
     */
    public static function server($name) {
        return $_SERVER[str_replace('-', '_', strtoupper($name))];
    }

    //-----------------------

    /**
     * 判断是什么方法
     * @param string $method
     * @return bool
     */
    public static function isMethod($method) {
        return strtoupper($_SERVER['REQUEST_METHOD']) === strtoupper($method);
    }

    /**
     * 判断是否是Ajax方法
     * @return bool
     */
    public static function isAjax(){
        return strtoupper(self::http('X_Requested_With')) === 'XMLHTTPREQUEST';
    }
}