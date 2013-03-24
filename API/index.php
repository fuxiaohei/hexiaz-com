<?php

error_reporting(E_ALL ^ E_NOTICE);

require_once '../HeXi/Hx.php';


Hx::init('API',__DIR__.'/');

Hx::run();

