<?php

class HeXiException extends Exception{

    protected $type = '';

    public function getType(){
        return $this->type;
    }

    public function __construct($message,$typeName){
        parent::__construct($message);
        $this->type = strtoupper($typeName);
    }

}
