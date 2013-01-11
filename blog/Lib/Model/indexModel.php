<?php
/**
 * Created by JetBrains PhpStorm.
 * User: FuXiaoHei
 * Date: 13-1-8
 * Time: 下午10:05
 * To change this template use File | Settings | File Templates.
 */
class indexModel extends BaseModel {

    public function init() {
        $this->table = 'Users';
        $this->db = Db::connect();
    }
}
