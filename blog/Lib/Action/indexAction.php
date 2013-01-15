<?php

HeXi::import('HeXi.Base.Controller');

class indexAction extends baseModel{

    public function index() {
        $this->useDb('mysql');
    }

}
