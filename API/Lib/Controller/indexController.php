<?php
/**
 * Created by JetBrains PhpStorm.
 * User: FuXiaoHei
 * Date: 13-3-24
 * Time: 下午10:59
 * To change this template use File | Settings | File Templates.
 */

class indexController extends Controller{

    public function indexAction(){
        Response::body('API is running!');
    }
}