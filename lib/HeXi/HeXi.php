<?php
define('HEXI_PATH', __DIR__ . '/');

require_once 'Core/Exception.php';

class HeXi {

    public static $name;

    public static $path;

    public static $config = array();

    public static $param = array();

    //---------------- init 过程 --------------

    private static function init() {
        ob_start();
        self::setExceptionHandler();
        if (self::loadConfigFile('app') === false) {
            self::exception('Application Configuration File is lost');
        }
        self::regAutoLoader();
    }

    private static function setExceptionHandler() {
        set_exception_handler(function (Exception $exc) {
            //ob_end_clean();
            echo $exc->getMessage();
            exit;
        });
    }

    private static function regAutoLoader() {
        spl_autoload_register(function ($className) {
            if (class_exists($className, false) || interface_exists($className, false)) {
                return true;
            }
            $class = array(
                'HeXi.Core.' . $className,
                HeXi::$name . '.Lib.ORG.' . $className
            );
            foreach ($class as $c) {
                if (self::import($c)) {
                    return true;
                }
            }
            self::exception('Application AutoLoader Error: ' . $className);
            return false;
        });
    }


    //----------------------------------

    public static function setup($name, $path) {
        if (!is_dir($path)) {
            self::exception('Application Directory Error: ' . $path);
        }
        self::$path = $path;
        self::$name = $name;
        self::init();
    }

    public static function run() {
        Router::auto();
        Response::send();
    }

    //-------------------

    public static $inc = array();

    public static function import($c) {
        if (self::$inc[$c]) {
            return true;
        }
        $file = null;
        if (stripos($c, 'HeXi.') === 0) {
            $file = str_replace(array('HeXi.', '.'), array(HEXI_PATH, '/'), $c) . '.php';
        }
        if (stripos($c, self::$name . '.') === 0) {
            $file = str_replace(array(self::$name . '.', '.'), array(self::$path, '/'), $c) . '.php';
        }
        if (stripos($c, 'Extend.') === 0) {
            $file = str_replace(array('Extend.', '.'), array(dirname(HEXI_PATH) . '/Extend/', '/'), $c) . '.php';
        }
        if (is_file($file)) {
            require_once $file;
            self::$inc[$c] = $file;
            return true;
        }
        return false;
    }

    public static function loadConfigFile($configFilename, $overwrite = false) {
        if (self::$config[$configFilename] && !$overwrite) {
            return self::$config[$configFilename];
        }
        $file = self::$path . 'conf/' . $configFilename . '.php';
        if (!is_file($file)) {
            return false;
        }
        self::$config[$configFilename] = require($file);
        return self::$config[$configFilename];
    }

    public static function exception($message, $type = 'APP') {
        throw new HeXiException($message, $type);
    }
}
