<?php


/**
 *
 */
class Router {

    /**
     * @var Router
     */
    private static $self;

    /**
     * @return Router
     */
    public static function instance() {
        return !self::$self ? self::$self = new Router() : self::$self;
    }

    /**
     * @return array
     */
    private function parseUrl() {
        $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $ext = pathinfo($url, PATHINFO_EXTENSION);
        if ($ext) {
            $url = str_replace('.' . $ext, '', $url);
        }
        $selfScript = $GLOBALS['config']['app']['router']['self'];
        if ($selfScript) {
            $url = str_replace('/' . $selfScript, '', $url);
        }
        return array_values(array_filter(explode('/', $url)));
    }

    /**
     * @param array $param
     * @return array
     */
    private function getCallback($param) {
        $callback = array();
        if ($param[1]) {
            $callback[] = array($param[0], $param[1]);
        }
        if ($param[0]) {
            $callback[] = array($param[0], $GLOBALS['config']['app']['controller']['action']);
            $callback[] = array($GLOBALS['config']['app']['controller']['default'], $param[0]);
        }
        $callback[] = array($GLOBALS['config']['app']['controller']['default'],
            $GLOBALS['config']['app']['controller']['action']
        );
        return $callback;
    }

    /**
     * @var array
     */
    private static $controllers = array();

    /**
     * @param string $name
     * @return bool|BaseController
     */
    private function getController($name) {
        if (!self::$controllers[$name]) {
            $controllerName = $name . 'Controller';
            if (!import(APP_NAME . '.Controller.' . $controllerName)) {
                return false;
            }
            self::$controllers[$name] = new $controllerName();
        }
        return self::$controllers[$name];
    }

    /**
     * @param array $callback
     * @return mixed
     */
    private function invokeController($callback) {
        $controller = false;
        $method     = null;
        foreach ($callback as $call) {
            $controller = $this->getController($call[0]);
            if ($controller) {
                if (is_callable(array($controller, $call[1]))) {
                    $method              = $call[1];
                    $GLOBALS['callback'] = array(get_class($controller), $method);
                    break;
                }
            }
        }
        if (!$controller) {
            exit('Router Exception');
        }
        return call_user_func(array($controller, $method));
    }

    /**
     * @return string
     */
    public function dispatch() {
        $param            = $this->parseUrl();
        $GLOBALS['param'] = $param;
        $callback         = $this->getCallback($param);
        return $this->invokeController($callback);
    }


}
