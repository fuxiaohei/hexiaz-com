<?php

//引入Widget抽象类
HeXi::import('HeXi.Base.Widget');

/**
 * Widget调用类
 */
class Widget {

    /**
     * Widget对象数组
     * @var array
     */
    private static $widgets = array();

    /**
     * 获取Widget实例
     * @param string $name
     * @return bool|baseWidget
     */
    public static function get($name) {
        if (!self::$widgets[$name]) {
            #从命令引入
            $command = HeXi::$name . '.Lib.Widget.' . $name . 'Widget';
            if (!HeXi::import($command)) {
                return false;
            }
            $wName  = $name . 'Widget';
            $widget = new $wName();
            if (!$widget instanceof baseWidget) {
                HeXi::exception($wName . ' is not a baseWidget ', __CLASS__);
            }
            self::$widgets[$name] = $widget;
        }
        return self::$widgets[$name];
    }

    /**
     * 执行Widget操作
     * @param string $command
     * @param array  $args
     * @return mixed|null
     */
    public static function action($command, $args = array()) {
        $command = explode('->', $command);
        $widget  = self::get($command[0]);
        if (!$widget) {
            return null;
        }
        if (!is_callable(array($widget, $command[1]))) {
            return null;
        }
        return call_user_func_array(array($widget, $command[1]), $args);
    }

    /**
     * 缓存对象
     * @var FileCache|AbstractCache
     */
    private static $cacheObject;

    /**
     * 获取并缓存Widget操作
     * @param string      $name
     * @param null|string $command
     * @param array       $args
     * @return mixed|null|string
     */
    public static function cache($name, $command = null, $args = array()) {
        #先要获取Cache对象
        if (!self::$cacheObject) {
            self::$cacheObject = Cache::get('widget');
        }
        #从缓存获取
        $data = self::$cacheObject->get($name);
        if (!$data) {
            if ($command) {
                #缓存中没有，执行操作，并缓存数据
                $data = self::action($command, $args);
                self::$cacheObject->put($name, $data);
            }
        }
        return $data;
    }

}
