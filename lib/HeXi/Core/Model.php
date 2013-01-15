<?php

//引入Model抽象类
HeXi::import('HeXi.Base.Model');

/**
 * Model调用类
 */
class Model {

    /**
     * Model实例数组
     * @var array
     */
    private static $models = array();

    /**
     * 获取Model实例
     * @param string $name Model名称
     * @return bool|baseModel
     */
    public static function get($name) {
        if (!self::$models[$name]) {
            $command = HeXi::$name . '.Lib.Model.' . $name . 'Model';
            #无法加载返回false
            if (!HeXi::import($command)) {
                return false;
            }
            $modelName = $name . 'Model';
            $model     = new $modelName();
            if (!$model instanceof baseModel) {
                HeXi::exception($modelName . ' is not a baseModel ', __CLASS__);
            }
            self::$models[$name] = $model;
        }
        return self::$models[$name];
    }

    /**
     * 执行Model的方法
     * @param string $command
     * @param array  $args
     * @return mixed|null
     */
    public static function action($command, $args = array()) {
        $command = explode('->', $command);
        $model   = self::get($command[0]);
        if (!$model) {
            return null;
        }
        if (!is_callable(array($model, $command[1]))) {
            return null;
        }
        return call_user_func_array(array($model, $command[1]), $args);
    }

    /**
     * Model需要的缓存对象
     * @var FileCache|AbstractCache
     */
    private static $cacheObject;

    /**
     * 调用并缓存Model命令
     * @param string      $name
     * @param null|string $command
     * @param array       $args
     * @return mixed|null
     */
    public static function cache($name, $command = null, $args = array()) {
        if (!self::$cacheObject) {
            self::$cacheObject = Cache::get('model');
        }
        $data = self::$cacheObject->get($name);
        if ($data) {
            #model的数据序列化保存
            $data = unserialize($data);
        } else {
            if ($command) {
                $data = self::action($command, $args);
                self::$cacheObject->put($name, serialize($data));
            } else {
                $data = null;
            }
        }
        return $data;
    }


}
