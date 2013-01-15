<?php

//引入控制器抽象类
HeXi::import('HeXi.Base.Controller');

/**
 * Restful控制器
 */
abstract class restController extends baseController {

    /**
     * 输出格式
     * @var mixed|string
     */
    protected $format;

    /**
     * 请求方式
     * @var string
     */
    protected $method;

    /**
     * 覆盖初始化方法
     */
    public function __construct() {
        parent::__construct();
        #设置输出格式
        if (!$this->format) {
            $type         = Request::suffix();
            $type         = !$type ? (isset($_GET['format']) ? $_GET['format'] : 'json') : $type;
            $this->format = $type;
        }
        #设置请求方式
        if (!$this->method) {
            $this->method = $this->realMethod();
        }
    }

    /**
     * 获取实际请求方式
     * @return mixed|string
     */
    protected function realMethod() {
        #自定义头的优先
        $method = Request::head('x-method');
        if (!$method) {
            $method = Request::$method;
        }
        return $method;
    }

    /**
     * 调用方法
     * @param string $name
     * @param array  $args
     */
    public function __call($name, $args = array()) {
        $realName = $name . $this->method;
        #补上请求方法
        if (method_exists($this, $realName)) {
            $this->$realName();
            return;
        }
        #不存在时，使用GET方法
        $realName = $name . 'GET';
        if (method_exists($this, $realName)) {
            $this->$realName();
            return;
        }
        #还不存在，使用默认方法的GET方法
        $default = HeXi::$config['app']['controller']['action'] . 'GET';
        if (!method_exists($this, $default)) {
            HeXi::exception('Router Mapping Error', 'router');
        }
        $this->$default();
        return;
    }


    /**
     * 处理数据为对应格式结果
     * @param mixed $data
     * @return bool
     */
    protected function result($data) {
        #json格式
        if ($this->format == 'json' || !$this->format) {
            Response::build(array(
                'contentType' => 'application/json',
                'body'        => json_encode($data)
            ));
            return true;
        }
        #xml格式
        if ($this->format == 'xml') {
            $charset = 'UTF-8';
            $string  = '<?xml version="1.0" encoding="' . $charset . '" ?>' . PHP_EOL . '<response>';
            $string .= $this->buildXml($data);
            $string .= PHP_EOL . '</response>';
            Response::build(array(
                'contentType' => 'text/xml',
                'charset'     => $charset,
                'body'        => $string
            ));
        }
        #html格式，展开数据
        if ($this->format == 'html') {
            Response::build(array(
                'contentType' => 'text/plain',
                'body'        => var_export($data, true)
            ));
            return true;
        }
        #php格式，序列化数据
        if ($this->format == 'php') {
            Response::build(array(
                'contentType' => 'text/plain',
                'body'        => serialize($data)
            ));
            return true;
        }
        return true;
    }

    /**
     * 组装xml字符串
     * @param array|string $data
     * @return string
     */
    private function buildXml($data) {
        if (is_array($data) || is_object($data)) {
            #处理数据为数组
            $data    = is_object($data) ? (array)$data : $data;
            $string = '';
            foreach ($data as $k => $v) {
                $string .= PHP_EOL . '<' . $k . '>' . $this->buildXml($v) . '</' . $k . '>';
            }
            return $string;
        }
        return htmlspecialchars($data, ENT_QUOTES);
    }

}
