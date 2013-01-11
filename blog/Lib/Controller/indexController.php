<?php
/**
 * Created by JetBrains PhpStorm.
 * User: FuXiaoHei
 * Date: 13-1-6
 * Time: 下午1:25
 * To change this template use File | Settings | File Templates.
 */ 
class indexController extends BaseController{

    public function index(){
        $class = new Test();
        //$foo1 = (&$class)->foo();
        $foo2 = & Test2::foo();
        var_dump($foo2);
        return '22';
    }
}

class Test{

    public function foo(){
        return time();
    }

}

class Test2{

    public static function foo(){
        return time();
    }
}
