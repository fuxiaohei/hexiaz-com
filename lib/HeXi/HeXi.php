<?php

define('HEXI_PATH', __DIR__ . DS);

require_once 'common.php';

import('HeXi.Core.Error', true);

import('HeXi.Core.Router', true);

class HeXi {

    private static $apps = array();

    /**
     * @param string $name
     * @param string $dir
     * @return HeXi
     */
    public static function create($name, $dir) {
        self::$apps[$name] = new HeXi($name, $dir);
        return self::$apps[$name];
    }

    public static function get($name) {
        return isset(self::$apps[$name]) ? self::$apps[$name] : false;
    }

    //------------------------------------


    //------------------------------------

    public function __construct($name, $dir) {
        if (!is_dir($dir)) {
            Error::stop('Application Directory Exception :' . $dir, 'APP');
        }
        define('APP_NAME', $name);
        define('APP_PATH', $dir);
        $this->prepare();
    }

    private $isRunning = false;

    private $response;

    private function prepare() {
        config('app');
        spl_autoload_register(function ($class) {
            $cmd = $GLOBALS['config']['app']['class'][$class];
            if (!$cmd) {
                Error::stop('Auto Loader Exception : ' . $class, 'APP');
            }
            import($cmd, true);
        });
    }


    private function sendResponse() {
        if ($this->response === true) {
            Response::instance()->send();
            exit;
        }
        if (is_string($this->response)) {
            Response::instance()
                ->content($this->response)
                ->send();
            exit;
        }
    }

    public function run() {
        $this->response  = Router::instance()->dispatch();
        $this->isRunning = true;
        $this->sendResponse();
    }

}
