<?php

error_reporting(E_ALL ^ E_NOTICE ^ E_STRICT);

define('NOW',time());

require 'lib/HeXi/HeXi.php';

HeXi::setup('Blog',__DIR__.'/blog/');

HeXi::run();
