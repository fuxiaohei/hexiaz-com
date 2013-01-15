<?php

abstract class AbstractCache {

    protected $config = array();

    public function __construct($name) {
        $this->config = HeXi::$config['cache'][$name];
        $this->init();
    }

    abstract protected function init();

    abstract public function put($key, $value);

    abstract public function get($key, $default = null);

    abstract public function delete($key);

    abstract public function expired($key, $default);

    abstract public function clear();

    abstract public function clean();

}
