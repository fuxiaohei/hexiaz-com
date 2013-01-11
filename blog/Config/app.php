<?php
/**
 * Created by JetBrains PhpStorm.
 * User: FuXiaoHei
 * Date: 13-1-5
 * Time: 下午11:07
 * To change this template use File | Settings | File Templates.
 */

return array(
    'class'      => array(
        'Router'         => 'HeXi.Core.Router',
        'Error'          => 'HeXi.Core.Error',
        //---------------
        'Request'        => 'HeXi.Http.Request',
        'Response'       => 'HeXi.Http.Response',
        'Input'          => 'HeXi.Http.Input',
        'Cookie'         => 'HeXi.Http.Cookie',
        'Session'        => 'HeXi.Http.Session',
        //---------------
        'View'           => 'HeXi.View.View',
        //---------------
        'Db'             => 'HeXi.Db.Db',
        'Sql'            => 'HeXi.Db.Sql',
        //--------------
        'BaseClass'      => 'HeXi.Base.Class',
        'BaseController' => 'HeXi.Base.Controller',
        'BaseModel'      => 'HeXi.Base.Model'
    ),
    'router'     => array(
        'self' => 'index.php'
    ),
    'controller' => array(
        'default' => 'index',
        'action'  => 'index'
    ),
    'view'       => array(
        'path'   => 'Tpl' . DS,
        'engine' => array(
            'name'   => 1,
            'expire' => 3600,
            'path'   => 'Runtime' . DS . 'Compile' . DS
        )
    )
);