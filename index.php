<?php

error_reporting(E_ALL ^ E_NOTICE);

define('DS', DIRECTORY_SEPARATOR);
define('NOW', time());

require_once 'lib/HeXi/HeXi.php';

HeXi::create('Blog', __DIR__ .DS.'blog'.DS)
    ->run();
