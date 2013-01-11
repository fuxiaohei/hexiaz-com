<?php
/**
 *
 *
 * @property bool get
 * @property bool post
 * @property bool delete
 * @property bool put
 * @property string method
 * @property string ip
 * @property string host
 * @property string agent
 * @property bool ajax
 *
 */
class Request {

    /**
     * @var Request
     */
    private static $self;

    /**
     * @return Request
     */
    public static function instance() {
        return !self::$self ? self::$self = new Request() : self::$self;
    }

    /**
     * @param null|string $method
     * @return bool|string
     */
    public static function method($method = null) {
        if ($method) {
            return strtoupper($_SERVER['REQUEST_METHOD']) == strtoupper($method);
        }
        return strtoupper($_SERVER['REQUEST_METHOD']);
    }

    /**
     * @param string $name
     * @return mixed
     */
    public static function server($name) {
        return $name === true ? $_SERVER : $_SERVER[strtoupper($name)];
    }

    /**
     * @param string $name
     * @return mixed
     */
    public static function header($name) {
        $header = strtoupper('http_' . str_replace('-', '_', $name));
        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            if (!empty($headers[$header])) {
                return $headers[$header];
            }
        }
        return $_SERVER[$header];
    }

    /**
     * @return string
     */
    public static function ip() {
        if ($_SERVER['HTTP_CLIENT_IP']) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }
        if ($_SERVER['HTTP_X_FORWARDED_FOR']) {
            $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return isset($ip[0]) ? trim($ip[0]) : '';
        }
        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * @return string
     */
    public static function host() {
        if ($_SERVER['HTTP_X_FORWARDED_FOR']) {
            $host = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $host = trim($host[count($host) - 1]);
        } else {
            $host = $_SERVER['HTTP_HOST'];
            if (!$host) {
                $host = $_SERVER['SERVER_NAME'];
            }
        }
        return $host;
    }

    /**
     * @return mixed
     */
    public static function agent() {
        return self::header('user_agent');
    }

    /**
     * @return bool
     */
    public static function ajax() {
        return 'XMLHttpRequest' == self::server('Http_X_Requested_With');
    }

    /**
     * @param string $key
     * @return bool|mixed|null|string
     */
    public function __get($key) {
        switch ($key) {
            case 'get':
                $this->get = $this->method('get');
                return $this->get;
            case 'post':
                $this->post = $this->method('post');
                return $this->post;
            case 'delete':
                $this->delete = $this->method('delete');
                return $this->delete;
            case 'put':
                $this->put = $this->method('put');
                return $this->put;
            case 'method';
                $this->method = $this->method();
                return $this->method;
            case 'ip':
                $this->ip = $this->ip();
                return $this->ip;
            case 'host':
                $this->host = $this->host();
                return $this->host;
            case 'agent':
                $this->agent = $this->agent();
                return $this->agent;
            case 'ajax':
                $this->ajax = $this->ajax();
                return $this->ajax;
        }
        return null;
    }
}
