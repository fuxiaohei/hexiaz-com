<?php

/**
 * 请求返回类
 * Class Response
 */
class Response {

    /**
     * 状态码
     * @var int
     */
    private static $status;

    /**
     * 设置和获取状态码
     * @param bool|int $status
     * @return int
     */
    public static function status($status = false) {
        if ($status === false) {
            return self::$status;
        }
        self::$status = (int)$status;
    }

    /**
     * 文本内容
     * @var string
     */
    private static $body;

    /**
     * 设置或获取内容
     * @param bool|string $content
     * @return string
     */
    public static function body($content = false) {
        if ($content === false) {
            return self::$body;
        }
        self::$body = $content;
    }

    /**
     * 头信息
     * @var array
     */
    private static $headers = array();

    /**
     * 状态码文字说明
     * @var array
     */
    protected static $statusTexts = array(
        '100' => 'Continue',
        '101' => 'Switching Protocols',
        '200' => 'OK',
        '201' => 'Created',
        '202' => 'Accepted',
        '203' => 'Non-Authoritative Information',
        '204' => 'No Content',
        '205' => 'Reset Content',
        '206' => 'Partial Content',
        '300' => 'Multiple Choices',
        '301' => 'Moved Permanently',
        '302' => 'Found',
        '303' => 'See Other',
        '304' => 'Not Modified',
        '305' => 'Use Proxy',
        '306' => '(Unused)',
        '307' => 'Temporary Redirect',
        '400' => 'Bad Request',
        '401' => 'Unauthorized',
        '402' => 'Payment Required',
        '403' => 'Forbidden',
        '404' => 'Not Found',
        '405' => 'Method Not Allowed',
        '406' => 'Not Acceptable',
        '407' => 'Proxy Authentication Required',
        '408' => 'Request Timeout',
        '409' => 'Conflict',
        '410' => 'Gone',
        '411' => 'Length Required',
        '412' => 'Precondition Failed',
        '413' => 'Request Entity Too Large',
        '414' => 'Request-URI Too Long',
        '415' => 'Unsupported Media Type',
        '416' => 'Requested Range Not Satisfiable',
        '417' => 'Expectation Failed',
        '500' => 'Internal Server Error',
        '501' => 'Not Implemented',
        '502' => 'Bad Gateway',
        '503' => 'Service Unavailable',
        '504' => 'Gateway Timeout',
        '505' => 'HTTP Version Not Supported',
    );

    /**
     * 创建返回类
     * @param string $body
     * @param int $status
     * @param string $contentType
     * @param array $headers
     * @param int $cache
     */
    public static function make($body, $status = 200, $contentType = 'text/html;charset=UTF-8', $headers = array(),$cache = 0) {
        self::status($status);
        self::body($body);
        self::$headers = array(
            'Content-Type' => $contentType,
            'X-Powered-By' => 'Hx - FuXiaoHei'
        ) + $headers;
        #设置缓存信息
        self::cache($cache);
    }

    /**
     * 设置缓存头
     * @param int $expire
     */
    public static function cache($expire = 0) {
        if ($expire <= 0) {
            self::$headers['Cache-Control'] = 'no-cache, no-store, max-age=0, must-revalidate';
            self::$headers['Expires'] = 'Mon, 26 Jul 1997 05:00:00 GMT';
            self::$headers['Pragma'] = 'no-cache';
        } else {
            self::$headers['Last-Modified'] = gmdate('r', time());
            self::$headers['Expires'] = gmdate('r', time() + $expire);
            self::$headers['Cache-Control'] = 'max-age=' . $expire;
            unset(self::$headers['Pragma']);
        }
    }

    /**
     * 重定向
     * 会立即发送头信息，此后的无效
     * @param string $url
     * @param int $code
     */
    public static function redirect($url, $code = 302) {
        self::status($code);
        self::$headers['location'] = $url;
        #重定向后直接发出头信息，不管此后的事情
        self::send();
        self::$isSend = 'redirect';
    }

    /**
     * 是否已经发送
     * 区分发送和重定向
     * @var bool|string
     */
    private static $isSend = false;

    /**
     * 获得是否发送状态
     * @return bool
     */
    public static function isSend() {
        return self::$isSend;
    }

    /**
     * 发送返回头
     */
    public static function send() {
        if (self::$isSend) {
            return;
        }
        #状态信息
        header('HTTP/1.1 ' . self::$status . ' ' . ucwords(self::$statusTexts[self::$status]));
        header('Status: ' . self::$status . ' ' . ucwords(self::$statusTexts[self::$status]));
        #头信息
        foreach (self::$headers as $key => $value) {
            header($key . ': ' . $value);
        }
        #输出内容
        if (self::$body) {
            echo self::$body;
        }
        self::$isSend = true;
    }
}