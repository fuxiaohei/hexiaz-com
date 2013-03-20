<?php

/**
 * 博客操作类
 * Class blogModel
 */
class blogModel extends Model {

    /**
     * 数据库连接
     * @var DbDriver|PDODriver
     */
    protected $db;

    /**
     * 构造函数
     */
    public function __construct() {
        $this->db = Db::connect();
    }

    /**
     * 获取博客列表
     * @param null|string $condition
     * @param int $page
     * @param int $size
     * @return array
     */
    public function getList($condition = null, $page = 1, $size = 4) {
        $sql = $this->sql('hx_node', 'node_id,node_title,node_key,node_description,create_time,comment_count,view_count,hx_node.category_id AS category_id,category_name,category_key')
            ->leftJoin('hx_category', 'hx_node.category_id = hx_category.category_id')
            ->where('status = "public"')
            ->where('node_type = "blog"')
            ->order('create_time DESC')
            ->page($page, $size);
        #处理附加条件
        if ($condition) {
            $sql->where($condition);
        }
        $sql = $sql->select();
        $data = $this->db->queryAll($sql, null, 'blog');
        return $data;
    }

    /**
     * 获取热评博客
     * @param int $size
     * @return array
     */
    public function getPop($size = 4) {
        $sql = $this->sql('hx_node', 'node_id,node_title,node_key,comment_count')
            ->where('status = "public"')
            ->where('node_type = "blog"')
            ->order('comment_count DESC')
            ->limit($size)
            ->select();
        return $this->db->queryAll($sql, null, 'blog');
    }

    /**
     * 获取博客一篇
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public function getOne($key, $value) {
        $sql = $this->sql('hx_node', 'node_id,node_title,node_key,node_content,create_time,comment_count,view_count,hx_node.category_id AS category_id,category_name,category_key')
            ->leftJoin('hx_category', 'hx_node.category_id = hx_category.category_id')
            ->where($key . ' = :' . $key)
            ->where('status = "public"')
            ->where('node_type = "blog"')
            ->select();
        return $this->db->query($sql, array($key => $value), 'blog');
    }

    /**
     * 更新博客阅读统计
     * @param int $node
     * @return bool|int
     */
    public function updateViewCount($node) {
        $sql = 'update hx_node set view_count = view_count + 1 WHERE node_id = :node_id';
        return $this->db->exec($sql, array('node_id' => $node));
    }

}