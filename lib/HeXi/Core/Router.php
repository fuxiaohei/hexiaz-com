<?php

/**
 * 路由类
 */
class Router {

    /**
     * 解析路由请求
     * @param string $url
     * @param bool   $path
     * @param bool   $global
     * @return array
     */
    public static function parse($url, $path = true, $global = false) {
        #按照PATHINFO处理
        if ($path) {
            $param = array_values(array_filter(explode('/', $url)));
            if ($global) {
                HeXi::$param = $param;
            }
            $controllers = array();
            if ($param[1]) {
                $controllers[] = array($param[0] . 'Action', $param[1]);
            }
            if ($param[0]) {
                $controllers[] = array($param[0] . 'Action', HeXi::$config['app']['controller']['action']);
                $controllers[] = array(HeXi::$config['app']['controller']['default'] . 'Action', $param[0]);
            }
            $controllers[] = array(HeXi::$config['app']['controller']['default'] . 'Action',
                HeXi::$config['app']['controller']['action']);
            return $controllers;
        }
        #按照GET数据处理
        $controllerName = !isset($_GET['controller']) ? HeXi::$config['app']['controller']['default'] . 'Action' : trim($_GET['controller']) . 'Action';
        $methodName     = !isset($_GET['action']) ? HeXi::$config['app']['controller']['action'] : trim($_GET['action']);
        if ($global) {
            $param       = !isset($_GET['param']) ? array() : array_values(array_filter(explode('/', $_GET['param'])));
            HeXi::$param = $param;
        }
        return array(array($controllerName, $methodName));
    }

    /**
     * 执行解析的路由参数
     * @param array $param
     * @return bool|mixed
     */
    public static function execute($param) {
        foreach ($param as $action) {
            $controller = HeXi::$name . '.Lib.Action.' . $action[0];
            #获取Action
            if (HeXi::import($controller)) {
                $controller = new $action[0]();
                #调用Action操作
                if (is_callable(array($controller, $action[1]))) {
                    return call_user_func(array($controller, $action[1]));
                }
                unset($controller);
            }
        }
        HeXi::exception('Router Mapping Error', __CLASS__);
        return false;
    }

    /**
     * 自动路由
     * 以当前请求地址为PATH自动路由
     */
    public static function auto() {
        $url   = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $url   = str_replace('.' . pathinfo($url, PATHINFO_EXTENSION), '', $url);
        $param = self::parse($url, true, true);
        self::execute($param);
    }
}
