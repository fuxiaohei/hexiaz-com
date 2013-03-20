<?php


/**
 * 数据库调用类
 * Class Db
 */
class Db {

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
            #加载数据库的配置文件
            $configFile = Hx::$path . 'db.config.php';
            if (!is_file($configFile)) {
                throw new Exception('数据库配置丢失');
            }
            self::$config = require_once($configFile);
        }
    }

    /**
     * 连接对象数组
     * @var array
     */
    private static $conn = array();

    /**
     * 连接到某个标识的数据库
     * @param string $name
     * @return DbDriver|PDODriver
     * @throws Exception
     */
    public static function connect($name = 'default') {
        #先初始化获取配置
        Db::init();
        if (!self::$conn[$name]) {
            #检查配置信息
            if (!self::$config[$name]) {
                throw new Exception('数据库 "' . $name . '" 配置丢失');
            }
            #根据类型载入驱动类
            switch (strtoupper(self::$config[$name]['driver'])) {
                case 'PDO':
                    Hx::import('Hx/Db/Driver/PDO');
                    self::$conn[$name] = new PDODriver(self::$config[$name]);
                    break;
            }
            if (!self::$conn[$name]) {
                throw new Exception('数据库 "' . $name . '" 不支持');
            }
        }
        return self::$conn[$name];
    }

    /**
     * 判断是否已经连接
     * @param string $name
     * @return bool
     */
    public static function isConnected($name){
        return self::$conn[$name] instanceof DbDriver;
    }
}


abstract class DbDriver {

    public $driver;

    protected $config;

    public function __construct($config){
        #构造时添加配置数据
        $this->config = $config;
        $this->driver = strtoupper($config['driver']);
    }

    abstract public function exec($sql,$param = array());

    abstract public function query($sql,$param = array(),$fetchClass = null);

    abstract public function queryAll($sql,$param = array(),$fetchClass = null);

    abstract public function lastId();

    abstract public function rowCount();

    abstract public function beginTransaction();

    abstract public function commit();

    abstract public function rollback();

}