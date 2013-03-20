<?php

error_reporting(E_ALL ^ E_NOTICE);

require_once '../HeXi/Hx.php';


Hx::init('Blog',__DIR__.'/');

Hx::run();

