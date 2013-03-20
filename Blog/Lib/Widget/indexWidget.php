<?php
/**
 * Created by JetBrains PhpStorm.
 * User: FuXiaoHei
 * Date: 13-3-14
 * Time: 下午6:50
 * To change this template use File | Settings | File Templates.
 */

class indexWidget extends Widget{

    public function render(){
        return $this->renderFile(Hx::$path.'Tpl/index.html',array());
    }

}