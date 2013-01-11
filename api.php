<?php
/**
 * Created by JetBrains PhpStorm.
 * User: FuXiaoHei
 * Date: 13-1-5
 * Time: 下午9:19
 * To change this template use File | Settings | File Templates.
 */

error_reporting(E_ALL ^ E_NOTICE);

define('DS', DIRECTORY_SEPARATOR);
define('NOW', time());

require_once 'lib/HeXi/HeXi.php';

HeXi::create('Api', __DIR__ .DS.'api'.DS)
    ->run();
