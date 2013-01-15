<?php

//引入数据库抽象类
HeXi::import('HeXi.Database.Driver.Abstract');

/**
 * 数据库类
 */
class Db {

    /**
     * 驱动对应的类名称
     * @var array
     */
    private static $class = array(
        'PDO' => array('PdoDriver', 'HeXi.Database.Driver.Pdo')
    );

    /**
     * 连接对象
     * @var array
     */
    private static $connections = array();


    /**
     * 连接到数据库
     * @param string $name
     * @return AbstractDriver|PdoDriver
     */
    public static function connect($name = 'default') {
        if (!self::$connections[$name]) {
            #加载数据库配置
            if (!HeXi::$config['database']) {
                HeXi::loadConfigFile('database');
            }
            #获取驱动类型
            $driver = strtoupper(HeXi::$config['database'][$name]['driver']);
            if (!$driver) {
                HeXi::exception('Database "' . $name . '" Configuration is missing !', __CLASS__);
            }
            $classData = self::$class[$driver];
            if (!$classData) {
                HeXi::exception('Database "' . $name . '" Driver is not supported !', __CLASS__);
            }
            #创建数据库类
            HeXi::import($classData[1]);
            $class                    = $classData[0];
            self::$connections[$name] = new $class($name);
        }
        return self::$connections[$name];
    }

    /**
     * 断开连接
     * @param string $name
     */
    public static function disConnect($name) {
        unset(self::$connections[$name]);
    }
}
