<?php

HeXi::import('Extend.Base.tableModel');

class indexModel extends tableModel {

    protected function init() {
        $this->db      = 'default';
        $this->table   = 'user';
        $this->primary = 'id';
    }

    public function now() {
        $data['id_new'] = 88;
        $data['id'] = 127;
        $data['name'] = '傅小黑';
        $data['sex'] = 'male';
        $data['age'] = 22;
        $this->save($data,'id');
        return array(NOW, uniqid(), microtime(true));
    }
}
