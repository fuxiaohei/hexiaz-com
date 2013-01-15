<?php

/**
 *  请求类
 */
class Request {

    /**
     * GET数据
     * @var array
     */
    public static $get;

    /**
     * POST数据
     * @var string
     */
    public static $post;

    /**
     * REQUEST数据
     * @var array
     */
    public static $request;

    /**
     * 请求方式
     * @var string
     */
    public static $method;

    /**
     * 路由参数
     * @var array
     */
    public static $param;

    /**
     * 是否Ajax请求
     * @var bool
     */
    public static $ajax;

    /**
     * 初始化 在Controller时初始化
     */
    public static function init() {
        self::$get     = & $_GET;
        self::$post    = & $_POST;
        self::$request = & $_REQUEST;
        self::$param   = & HeXi::$param;
        self::$method  = strtoupper($_SERVER['REQUEST_METHOD']);
        self::$ajax    = ('XMLHttpRequest' == self::head('X_REQUESTED_WITH'));
    }

    /**
     * 请求方式
     * @param null|string $method
     * @return bool|string
     */
    public static function method($method = null) {
        return $method === null ? self::$method : (self::$method == strtoupper($method));
    }

    /**
     * SERVER数据
     * @param bool|string $name
     * @return mixed
     */
    public static function server($name = true) {
        return $name === true ? $_SERVER : $_SERVER[strtoupper($name)];
    }

    /**
     * HTTP头数据
     * @param string $name
     * @return mixed
     */
    public static function head($name) {
        $name = str_replace('-', '_', strtoupper('HTTP_' . $name));
        return $_SERVER[$name];
    }

    /**
     * ip地址
     * @var string
     */
    protected static $ip;

    /**
     * 获取IP地址
     * @return string
     */
    public static function ip() {
        if (!self::$ip) {
            /**
             * 来自 ColaPHP 的代码
             */
            $keys = array('HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'REMOTE_ADDR');
            foreach ($keys as $key) {
                if (empty($_SERVER[$key])) continue;
                $ips = explode(',', $_SERVER[$key], 1);
                $ip  = $ips[0];
                if (false != ip2long($ip) && long2ip(ip2long($ip) === $ip)) {
                    self::$ip = $ip;
                }
            }
            if (!self::$ip) {
                self::$ip = 'unknown';
            }
        }
        return self::$ip;
    }

    /**
     * 基本URL地址
     * @var string
     */
    protected static $baseUrl;

    /**
     * URL地址
     * @return string
     */
    public static function baseUrl() {
        if (!self::$baseUrl) {
            $schema = ('on' == self::server('HTTPS')) ? 'https' : 'http';
            $url    = $schema . "://" . self::server('SERVER_NAME');
            $port   = self::server('SERVER_PORT');
            if (80 != $port) {
                $url .= ":{$port}";
            }
            self::$baseUrl = $url . '/';
        }
        return self::$baseUrl;
    }

    /**
     * 当前访问URL地址
     * @return string
     */
    public static function currentUrl() {
        return self::baseUrl() . ltrim($_SERVER['REQUEST_URI'], '/');
    }

    /**
     * 后缀名
     * @var string
     */
    protected static $suffix;

    /**
     * 获取后缀名
     * @return mixed|string
     */
    public static function suffix(){
        if(!self::$suffix){
            $suffix = pathinfo(parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH),PATHINFO_EXTENSION);
            self::$suffix = $suffix;
        }
        return self::$suffix;
    }

}
