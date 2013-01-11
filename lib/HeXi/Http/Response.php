<?php

/**
 *
 */
class Response {

    /**
     * @var Response
     */
    private static $self;

    /**
     * @return Response
     */
    public static function instance() {
        return !self::$self ? self::$self = new Response() : self::$self;
    }

    /**
     * @var int
     */
    protected $status;

    /**
     * @var bool
     */
    protected $content;

    /**
     * @var string
     */
    protected $contentType;

    /**
     * @var string
     */
    protected $charset;

    /**
     * @var array
     */
    protected $header;

    /**
     * @var bool
     */
    protected $isSend;

    /**
     *
     */
    private function __construct() {
        $this->status      = 200;
        $this->contentType = 'text/html';
        $this->charset     = 'utf-8';
        $this->header      = array(
            'X-Author'     => 'FuXiaoHei',
            'X-Powered-By' => 'HeXi 2.0 alpha'
        );
        $this->content     = false;
        $this->isSend      = false;
        $this->setNoCache();
    }

    /**
     *
     */
    private function setNoCache() {
        $this->header['Cache-Control'] = 'no-cache, no-store, max-age=0, must-revalidate';
        $this->header['Expires']       = 'Mon, 26 Jul 1997 05:00:00 GMT';
        $this->header['Pragma']        = 'no-cache';
    }

    /**
     * @param int $expire
     * @return Response
     */
    public function cache($expire = 0) {
        if ($expire < 1) {
            $this->setNoCache();
        } else {
            $this->header['Last-Modified'] = gmdate('r', time());
            $this->header['Expires']       = gmdate('r', time() + $expire);
            $this->header['Cache-Control'] = 'max-age=' . $expire;
        }
        return $this;
    }

    /**
     * @param array $options
     * @return Response
     */
    public function build($options) {
        if (isset($options['status'])) {
            $this->status = $options['status'];
        }
        if (isset($options['contentType'])) {
            $this->contentType = $options['contentType'];
        }
        if (isset($options['charset'])) {
            $this->charset = $options['charset'];
        }
        if (isset($options['cache'])) {
            $this->cache($options['cache']);
        }
        if (isset($options['header'])) {
            $this->header = $options['header'] + $this->header;
        }
        return $this;
    }

    /**
     * @param string $key
     * @param null|string $value
     * @return Response
     */
    public function header($key, $value = null) {
        if ($value === false || $value === null) {
            unset($this->header[$key]);
            return $this;
        }
        $this->header[$key] = $value;
        return $this;
    }

    /**
     * @param string $url
     * @param bool $forever
     * @return Response
     */
    public function redirect($url, $forever = false) {
        $this->header['Location'] = $url;
        $this->status             = $forever === true ? 301 : 302;
        return $this;
    }

    /**
     * @param string $cnt
     * @return Response
     */
    public function content($cnt) {
        $this->content = $cnt;
        return $this;
    }

    /**
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
     * @return bool
     */
    public function send() {
        if ($this->isSend) {
            return true;
        }
        #状态信息
        header('HTTP/1.1 ' . $this->status . ' ' . ucwords(self::$statusTexts[$this->status]));
        header('Status: ' . $this->status . ' ' . ucwords(self::$statusTexts[$this->status]));
        #文档信息
        if (strstr($this->contentType, 'text/')) {
            header('Content-Type:' . $this->contentType . ';charset=' . $this->charset);
        } else {
            header('Content-Type:' . $this->contentType);
        }
        #头信息
        foreach ($this->header as $key => $value) {
            header($key . ': ' . $value);
        }
        $this->isSend = true;
        #输出内容
        if ($this->content) {
            echo $this->content;
            return true;
        }
        return true;
    }

}
