<?php

/**
 * 控制器抽象类
 */
abstract class baseController {

    /**
     * 构造方法
     */
    public function __construct() {
        #初始化请求类和返回类
        Request::init();
        Response::init();
        $this->init();
    }

    /**
     * 代替的初始化方法
     */
    protected function init() {

    }

    /**
     * 添加视图数据
     * @param array $data
     * @return $this
     */
    protected function assign($data) {
        View::$data = $data + View::$data;
        return $this;
    }

    /**
     * 显示视图
     * @param string $tpl
     */
    protected function display($tpl) {
        $string = View::render($tpl);
        Response::body($string);
    }

    /**
     * 重定向
     * @param string $url
     */
    protected function redirect($url) {
        Response::redirect($url);
    }

    /**
     * 执行URL
     * 自定义的路由操作
     * @param string $url
     * @return bool|mixed
     */
    protected function url($url) {
        $param = Router::parse($url);
        return Router::execute($param);
    }

    /**
     * Ajax返回json数据
     * @param array $data
     * @param bool  $status
     */
    protected function ajax($data, $status = true) {
        $json['response'] = (bool)$status;
        $json['data']     = $data;
        Response::build(array(
            'contentType' => 'application/json',
            'body'        => json_encode($json)));
    }

    /**
     * 显示错误页面
     * @param string $message
     * @param int  $code
     * @param string $tpl
     */
    protected function error($message, $code = 500, $tpl = null) {
        Response::build(array('status' => $code));
        if ($tpl) {
            $this->display($tpl);
        } else {
            Response::body($message);
        }
    }

}

