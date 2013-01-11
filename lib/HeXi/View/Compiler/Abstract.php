<?php

abstract class AbstractCompiler {

    protected function __construct($option = array()){
        $this->set($option);
    }

    abstract public function set();

    abstract public function compile($string);

    abstract public function save($key,$string);

    abstract public function read($key);

    abstract public function get($key);
}
