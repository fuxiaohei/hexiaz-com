<?php

/**
 * 控制器基类
 * Class Controller
 */
abstract class Controller {

    /**
     * 视图数据
     * @var array
     */
    private $viewData = array();

    /**
     * 判断请求类型
     * @param string $name
     * @return bool
     */
    protected function is($name) {
        if (strtolower($name) == 'ajax') {
            return Request::isAjax();
        }
        return Request::isMethod($name);
    }

    /**
     * 设置视图数据
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    protected function assign($name, $value) {
        $this->viewData[$name] = $value;
        return $this;
    }

    /**
     * 显示模板
     * @param string $tpl
     */
    protected function display($tpl) {
        #把视图渲染结果添加到返回类中去
        Response::make(View::make(Hx::$path . 'Tpl/')
            ->with($this->viewData)
            ->render($tpl));
    }

    /**
     * 设置json数据返回
     * @param mixed $data
     */
    protected function json($data) {
        Response::make(json_encode($data), 200, 'application/json;charset=UTF-8');
    }

    /**
     * 重定向
     * @param string $url
     */
    protected function redirect($url) {
        Response::redirect($url);
    }
}