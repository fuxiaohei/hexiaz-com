<?php


/**
 * 分类操作类
 * Class categoryModel
 */
class categoryModel extends Model {

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
     * 获取所有分类
     * @return array
     */
    public function getAll() {
        $sql = $this->sql('hx_category', 'category_id,category_name,category_key,category_description,node_count')
            //->where('is_active >= 10')
            ->order('node_count DESC')
            ->select();
        return $this->db->queryAll($sql, null, 'category');
    }

    /**
     * 获取一个分类
     * @param string $key
     * @param mixed $value
     * @return categoryStore
     */
    public function getOne($key, $value) {
        $sql = $this->sql('hx_category', 'category_id,category_key,category_name,category_description,node_count')
            ->where($key . ' = :' . $key)
            ->select();
        return $this->db->query($sql, array($key => $value), 'category');
    }
}