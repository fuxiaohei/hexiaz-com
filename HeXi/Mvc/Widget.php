<?php

/**
 * Widget基础类
 * Class Widget
 */
abstract class Widget {

    /**
     * 渲染方法
     * @return mixed
     */
    abstract public function render();

    /**
     * 渲染文件
     * @param string $file
     * @param array $data
     * @return string
     */
    protected function renderFile($file, array $data) {
        $dir = dirname($file) . '/';
        $file = basename($file);
        return View::make($dir, true, $data)->render($file);
    }

    private static $widgets;

    private static function get($name) {
        if (!self::$widgets[$name]) {
            if (!Hx::import(Hx::$name . '/Lib/Widget/' . $name . 'Widget', false)) {
                return null;
            }
            $widget = $name . 'Widget';
            self::$widgets[$name] = new $widget();
        }
        return self::$widgets[$name];
    }

    /**
     * 执行某个组件渲染
     * @param string $name
     * @return mixed|null
     */
    public static function exec($name) {
        if (self::$cacheName) {
            $cache = Cache::instance(self::$cacheName);
            $content = $cache->get($name . 'Widget::render');
            if ($content) {
                return $content;
            }
            $widget = self::get($name);
            if (!$widget) {
                return null;
            }
            $content = call_user_func(array($widget, 'render'));
            $cache->set($name . 'Widget::render', $content, 3600);
            return $content;
        } else {
            $widget = self::get($name);
            if (!$widget) {
                return null;
            }
            return call_user_func(array($widget, 'render'));
        }
    }

    //-------------------

    /**
     * Widget调用缓存标识
     * @var bool
     */
    private static $cacheName = false;

    /**
     * 设置缓存标识
     * @param string $name
     */
    public static function setCache($name = 'default') {
        self::$cacheName = $name;
    }
}