<?php
/**
 * Created by JetBrains PhpStorm.
 * User: FuXiaoHei
 * Date: 13-3-25
 * Time: 下午11:14
 * To change this template use File | Settings | File Templates.
 */

class crxModel extends Model {

    protected $db;

    public function __construct() {
        $this->db = Db::connect();
    }

    public function preData() {
        $sql = $this->sql('hx_node', 'node_id,node_title,node_key,create_time')
            ->where('node_type = "blog"')
            ->where('status <> "public"')
            ->order('node_id DESC')
            ->select();
        $nodes = $this->db->queryAll($sql);
        $sql = $this->sql('hx_comment', 'comment_id,author,author_email,author_url,create_time,content')
            ->where('comment_type = "comment"')
            ->where('status <> "public"')
            ->order('create_time DESC')
            ->select();
        $comments = $this->db->queryAll($sql);
        return array('node' => $nodes, 'comment' => $comments);
    }
}