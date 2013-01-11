<?php

import('HeXi.Db.Driver.Abstract');

class Db {

    /**
     * 驱动对应的类名称
     * @var array
     */
    private static $class = array(
        'PDO' => array('PdoDriver', 'HeXi.Db.Driver.Pdo')
    );

    private static $connections = array();


    /**
     * 连接到数据库
     * @param string $name
     * @return AbstractDriver|PdoDriver
     */
    public static function connect($name = 'default') {
        if (!self::$connections[$name]) {
            if (!$GLOBALS['config']['database']) {
                config('database');
            }
            $driver = strtoupper($GLOBALS['config']['database'][$name]['driver']);
            if (!$driver) {
                exit('Database "' . $name . '" Configuration is missing !');
            }
            $classData = self::$class[$driver];
            if (!$classData) {
                exit('Database "' . $name . '" Driver is not supported !');
            }
            import($classData[1]);
            $class = $classData[0];
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
