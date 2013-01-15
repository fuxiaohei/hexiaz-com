<?php

//引入Cache的配置信息
if (!HeXi::loadConfigFile('cache')) {
    HeXi::exception('Application Cache Configuration File is lost', 'cache');
}

//引入Cache抽象类
HeXi::import('HeXi.Cache.Abstract');

/**
 * Cache缓存调用类
 */
class Cache {

    /**
     * 缓存对象数组
     * @var array|AbstractCache
     */
    private static $object = array();

    /**
     * 获取缓存对象
     * @param string $name 名称
     * @return null|AbstractCache|FileCache
     */
    public static function get($name) {
        #判断是否有已经存在
        if (self::$object[$name]) {
            return self::$object[$name];
        }
        #获取缓存类型
        $type = HeXi::$config['cache'][$name]['type'];
        if (!$type) {
            return null;
        }
        $cache = null;
        #获取缓存实例
        switch (strtoupper($type)) {
            case 'FILE':
                HeXi::import('HeXi.Cache.File');
                $cache = new FileCache($name);
                break;
            default:
                $cache = null;
                break;
        }
        if (!$cache) {
            return null;
        }
        self::$object[$name] = $cache;
        return self::$object[$name];
    }

    /**
     * 缓存清理
     * @param string $name 缓存名称
     * @return bool
     */
    public static function clean($name) {
        $cache = isset(self::$object[$name]) ? self::$object[$name] : self::get($name);
        return $cache->clean();
    }
}
