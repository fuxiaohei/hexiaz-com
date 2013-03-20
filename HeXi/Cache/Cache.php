<?php
/**
 * Created by JetBrains PhpStorm.
 * User: FuXiaoHei
 * Date: 13-3-14
 * Time: 下午2:42
 * To change this template use File | Settings | File Templates.
 */

class Cache {

    /**
     * 配置信息类
     * @var array
     */
    private static $config;

    /**
     * 初始化
     * 获取配置信息
     * @throws Exception
     */
    private static function init() {
        if (!self::$config) {
            $configFile = Hx::$path . 'cache.config.php';
            if (!is_file($configFile)) {
                throw new Exception('缓存配置丢失');
            }
            self::$config = require_once($configFile);
        }
    }

    /**
     * 缓存对象数组
     * @var array
     */
    private static $handlers = array();

    /**
     * 获取缓存实例
     * @param string $name
     * @return CacheDriver|FileCache
     * @throws Exception
     */
    public static function instance($name = 'default') {
        #初始化
        self::init();
        #获取handler
        if (!self::$handlers[$name]) {
            if (!self::$config[$name]) {
                throw new Exception('缓存 "' . $name . '" 配置丢失');
            }
            #分类型加载不同的缓存对象
            switch (strtoupper(self::$config[$name]['type'])) {
                case 'FILE':
                    Hx::import('Hx/Cache/Driver/File');
                    self::$handlers[$name] = new FileCache(self::$config[$name]);
                    break;
            }
            if (!self::$handlers[$name]) {
                throw new Exception('缓存 "' . $name . '" 不支持');
            }
        }
        return self::$handlers[$name];
    }
}

abstract class CacheDriver {

    protected $options;

    public function __construct($options) {
        $this->options = $options;
    }

    abstract public function set($key, $value, $expire);

    abstract public function get($key, $ignoreExpire = false);

    abstract public function rm($key);

    abstract public function clear();

    abstract protected function gc();

    public function __destruct() {
        $this->gc();
    }
}