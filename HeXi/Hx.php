<?php

/**
 * Hx核心库类
 * Class Hx
 */
class Hx {

    /**
     * 应用名称
     * @var string
     */
    public static $name;

    /**
     * 应用地址
     * @var string
     */
    public static $path;

    /**
     * 初始化应用信息
     * @param string $name
     * @param string $path
     */
    public static function init($name, $path) {
        #初始化应用的名称和地址，以便import方法有效的引入对应库类
        self::$name = $name;
        self::$path = $path;
    }

    //---------------------

    /**
     * 自动加载数组
     * @var array
     */
    private static $autoClass = array(
        'Router' => 'Hx/Core/Router',
        'Input' => 'Hx/Http/Input',
        'Request' => 'Hx/Http/Request',
        'Response' => 'Hx/Http/Response',
        'Controller' => 'Hx/Mvc/Controller',
        'Widget' => 'Hx/Mvc/Widget',
        'Model' => 'Hx/Mvc/Model',
        'View' => 'Hx/Mvc/View',
        'Sql' => 'Hx/Db/Sql',
        'Db' => 'Hx/Db/Db',
        'Cache' => 'Hx/Cache/Cache'
    );

    /**
     * 已经加载数据
     * @var array
     */
    private static $autoLoaded = array();

    /**
     * 添加自动加载的类
     * @param array $classes
     */
    public static function autoClass(array $classes) {
        self::$autoClass += $classes;
    }

    /**
     * 引入文件
     * @param string $command
     * @param bool $throw
     * @return bool
     * @throws Exception
     */
    public static function import($command, $throw = true) {
        #判断是不是已经加载了
        if (self::$autoLoaded[$command]) {
            return true;
        }
        $file = '';
        #加载核心类，Hx/XXX/XXX
        if (strpos($command, 'Hx') === 0) {
            $file = str_replace('Hx/', __DIR__ . '/', $command) . '.php';
        }
        #加载应用的类，Name/XXX/XXX
        if (strpos($command, self::$name) === 0 && !is_file($command)) {
            $file = str_replace(self::$name . '/', self::$path, $command) . '.php';
        }
        #加载失败，抛出错误或者返回错误
        if (!is_file($file)) {
            if (!$throw) {
                return false;
            }
            throw new Exception('无法加载"' . $command . '"');
        }
        #加载文件并记录
        require_once $file;
        self::$autoLoaded[$command] = $file;
        return true;
    }

    //--------------------------

    /**
     * 异常处理函数
     * @var null|Closure
     */
    private static $excHandler = null;

    /**
     * 设置异常处理函数
     * @param callable $func
     */
    public static function excHandler(Closure $func) {
        self::$excHandler = $func;
    }

    //------------------------

    /**
     * 注册自动加载函数
     * @param string $className
     * @throws Exception
     */
    protected  static function regAutoLoad($className) {
        if (!self::$autoClass[$className]) {
            throw new Exception('无法自动加载"' . $className . '"');
        }
        $command = self::$autoClass[$className];
        #根据保存的自动加载类列表自动加载
        self::import($command);
    }

    /**
     * 运行应用
     */
    public static function run() {
        if (!self::$name || !self::$path) {
            exit('无效的应用');
        }
        if (!self::$excHandler) {
            self::$excHandler = function (Exception $exc) {
                echo $exc;
                die;
            };
        }
        set_exception_handler(self::$excHandler);
        spl_autoload_register('Hx::regAutoLoad');
        Router::map();
        Response::send();
    }
}