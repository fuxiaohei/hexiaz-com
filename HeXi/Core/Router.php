<?php

/**
 * 路由操作类
 * Class Router
 */
class Router {

    /**
     * 路由URL
     * @var string
     */
    public static $url;

    /**
     * 请求的后缀格式
     * @var string
     */
    public static $format;

    /**
     * 路由参数
     * @var array
     */
    public static $param;

    /**
     * 解析url
     */
    private static function parseUrl() {
        $ext = pathinfo(self::$url, PATHINFO_EXTENSION);
        #处理后缀名，作为请求格式
        if ($ext) {
            self::$format = $ext;
            self::$param = explode('/', str_replace('.' . $ext, '', self::$url));
            self::$param = array_values(array_filter(self::$param));
        } else {
            #设置默认情况
            self::$format = 'html';
            self::$param = explode('/', self::$url);
            self::$param = array_values(array_filter(self::$param));
        }
    }

    /**
     * 自动路由处理
     * @return array
     */
    private static function detectAutoMap() {
        $auto = array();
        #填写自动路由处理的所有可能
        if (self::$param[1]) {
            $auto[] = array(self::$param[0], self::$param[1]);
        }
        if (self::$param[0]) {
            $auto[] = array(self::$param[0], 'index');
            $auto[] = array('index', self::$param[0]);
        }
        $auto[] = array('index', 'index');
        return $auto;
    }

    /**
     * 路由分发
     * @param null|string $url
     * @return mixed
     * @throws Exception
     */
    public static function map($url = null) {
        if (!$url) {
            $url = $_SERVER['REQUEST_URI'];
        }
        #分割处理URL，不需要请求字符串和Hash
        self::$url = parse_url($url, PHP_URL_PATH);
        self::parseUrl();
        #获取自动结果
        $autoMap = self::detectAutoMap();
        foreach ($autoMap as $map) {
            #获取控制器文件
            $controller = $map[0] . 'Controller';
            $file = Hx::$path . 'Lib/Controller/' . $controller . '.php';
            if (!is_file($file)) {
                continue;
            }
            require_once $file;
            #调用控制器方法
            $controller = new $controller();
            if (!is_callable(array($controller, $map[1] . 'Action'))) {
                continue;
            }
            return call_user_func(array($controller, $map[1] . 'Action'));
        }
        #路由失败，抛出异常
        throw new Exception('路由分发失败');
    }
}