<?php
/**
 * Created by JetBrains PhpStorm.
 * User: FuXiaoHei
 * Date: 13-1-5
 * Time: 下午11:17
 * To change this template use File | Settings | File Templates.
 */
abstract class BaseClass {

    protected $config;

    protected $param;

    protected $called;

    public function __construct() {
        $this->config = & $GLOBALS['config'];
        $this->param  = & $GLOBALS['param'];
        $this->called = & $GLOBALS['callback'];
    }

}
