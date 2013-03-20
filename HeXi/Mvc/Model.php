<?php

/**
 * 模型类基类
 * Class Model
 */
abstract class Model {

    /**
     * 获取SQL对象
     * @param string $table
     * @param string $col
     * @return Sql
     */
    protected final function sql($table, $col = '*') {
        return Sql::make($table, $col);
    }

    //-------------------------------------------------

    /**
     * 模型实例数组
     * @var array
     */
    private static $models;

    /**
     * 获取模型
     * @param string $name
     * @return Model
     */
    public static function get($name) {
        if (!self::$models[$name]) {
            Hx::import(Hx::$name . '/Lib/Model/' . $name . 'Model');
            $model = $name . 'Model';
            self::$models[$name] = new $model();
        }
        return self::$models[$name];
    }

    /**
     * 调用缓存标识
     * @var bool
     */
    private static $cacheName = false;

    /**
     * 设置缓存
     * @param string $name
     */
    public static function setCache($name) {
        self::$cacheName = $name;
    }

    /**
     * 执行模型操作
     * @param string $name
     * @param string $method
     * @param array $args
     * @return mixed|null
     */
    public static function exec($name, $method, $args = array()) {
        if (self::$cacheName) {
            #获取缓存对象
            $cache = Cache::instance($name);
            $key = $name . 'Model::' . $method . '(' . join(',', $args) . ')';
            $data = $cache->get($key);
            #获取缓存并返回
            if ($data) {
                return $data;
            }
            #缓存失效了，获取模型对象并调用方法
            $model = self::get($name);
            $data = call_user_func_array(array($model, $method), $args);
            #保存新的缓存
            $cache->set($key, $data, 7200);
            return $data;
        } else {
            $model = self::get($name);
            return call_user_func_array(array($model, $method), $args);
        }
    }
}