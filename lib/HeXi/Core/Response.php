<?php

/**
 * 请求返回类
 */
class Response {

    /**
     * 状态码描述
     * @var array
     */
    protected static $statusCode = array(
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
     * 状态码
     * @var int
     */
    private static $status;

    /**
     * 文档类型
     * @var string
     */
    private static $contentType;

    /**
     * 字符集
     * @var string
     */
    private static $charset;

    /**
     * 内容文本
     * @var string
     */
    private static $body = '';

    /**
     * 已经发送
     * @var bool
     */
    public static $isSend = false;

    /**
     * 头信息
     * @var array
     */
    private static $header = array();

    /**
     * 没有Cache
     */
    private function noCache() {
        self::$header['Cache-Control'] = 'no-cache, no-store, max-age=0, must-revalidate';
        self::$header['Expires']       = 'Mon, 26 Jul 1997 05:00:00 GMT';
        self::$header['Pragma']        = 'no-cache';
    }

    /**
     * 设置Cache
     * @param int $expire
     */
    private function setCache($expire = 0) {
        if ($expire > 0) {
            self::$header['Last-Modified'] = gmdate('r', time());
            self::$header['Expires']       = gmdate('r', time() + $expire);
            self::$header['Cache-Control'] = 'max-age=' . $expire;
            unset(self::$header['Pragma']);
        }
    }

    /**
     *  初始化
     */
    public static function init() {
        self::$status      = 200;
        self::$contentType = 'text/html';
        self::$charset     = 'utf-8';
        self::$header      = array(
            'X-Author'     => 'FuXiaoHei',
            'X-Powered-By' => 'HeXi 2.0 alpha',
        );
        self::$body        = '';
        self::noCache();
    }

    /**
     * 构建返回类
     * @param array $option
     */
    public static function build($option = array()) {
        if (!self::$status) {
            self::init();
        }
        if ($option['status']) {
            self::$status = (int)$option['status'];
        }
        if ($option['contentType']) {
            self::$contentType = (string)$option['contentType'];
        }
        if ($option['charset']) {
            self::$charset = (string)$option['charset'];
        }
        if ($option['body']) {
            self::$body = (string)$option['body'];
        }
        if ($option['cache'] > 0) {
            self::setCache($option['cache']);
        }
        if (is_array($option['header'])) {
            self::$header = $option['header'] + self::$header;
        }
    }

    /**
     * 添加文本内容
     * @param string $string
     */
    public static function body($string) {
        self::$body = $string;
    }

    /**
     * 重定向
     * 会直接发送返回信息
     * @param string $url
     * @param bool   $forever
     */
    public static function redirect($url, $forever = false) {
        self::$header['location'] = $url;
        self::$status             = $forever === true ? 301 : 302;
        self::$body               = null;
        self::send();
    }


    /**
     * 发送返回信息
     * @return bool
     */
    public static function send() {
        if (!self::$status) {
            self::init();
        }
        if (self::$isSend) {
            return true;
        }
        #状态信息
        header('HTTP/1.1 ' . self::$status . ' ' . ucwords(self::$statusCode[self::$status]));
        header('Status: ' . self::$status . ' ' . ucwords(self::$statusCode[self::$status]));
        #文档信息
        if (strstr(self::$contentType, 'text/')) {
            header('Content-Type:' . self::$contentType . ';charset=' . self::$charset);
        } else {
            header('Content-Type:' . self::$contentType);
        }
        #头信息
        foreach (self::$header as $key => $value) {
            header($key . ': ' . $value);
        }
        self::$isSend = true;
        #输出内容
        if (self::$body) {
            echo self::$body;
            return true;
        }
        return true;
    }
}
