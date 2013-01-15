<?php

//引入默认Model抽象类
HeXi::import('HeXi.Base.Model');

/**
 * 单表Table的Model抽象类
 */
abstract class tableModel extends baseModel {

    /**
     * 初始化方法
     */
    public function __construct() {
        //自定义的初始化
        $this->init();
        //根据自定义初始化的内容完成最终初始化
        $this->initialize();
    }

    /**
     * 最终初始化
     */
    private function initialize() {
        if ($this->db) {
            $this->db = $this->useDb($this->db);
        }
        if (!$this->table || !$this->primary) {
            HeXi::exception('Non-table Schema Info in TableModel', 'model');
        }
    }

    /**
     * 自定义初始化
     * @return mixed
     */
    abstract protected function init();

    /**
     * 表格名称
     * @var string
     */
    protected $table;

    /**
     * 数据库连接
     * @var AbstractDriver|PdoDriver
     */
    protected $db;

    /**
     * 主键名称
     * @var string
     */
    protected $primary;

    /**
     * 处理的字段，逗号隔开
     * @var null|string
     */
    protected $field = null;

    /**
     * 分页页码
     * @var int
     */
    protected $page = 1;

    /**
     * 分页大小
     * @var int
     */
    protected $size = 100;

    /**
     * 排序信息
     * @var null|string
     */
    protected $order = null;

    /**
     * 重置一些属性
     */
    protected function reset() {
        $this->field = null;
        $this->page  = 1;
        $this->size  = 100;
        $this->order = null;
    }

    /**
     * 查询一个数据
     * @param string $keyValue 主键的值，或者where条件
     * @param bool   $isKey 使用Where条件写false
     * @return array|bool|null|object
     */
    protected function find($keyValue, $isKey = true) {
        if (!$isKey) {
            $sql = $this->sql($this->table, (!$this->field ? '*' : $this->field))
                ->where($keyValue)->select();
            return $this->db->query($sql);
        }
        $sql = $this->sql($this->table, (!$this->field ? '*' : $this->field))
            ->where($this->primary . ' = :' . $this->primary)->select();
        $this->reset();
        return $this->db->query($sql, array($this->primary => $keyValue));
    }

    /**
     * 查询一组数据
     * 受到field，page和order的影响
     * @param null|string  $where
     * @param array        $args 绑定参数
     * @return array|null
     */
    protected function select($where = null, $args = array()) {
        $sql = $this->sql($this->table, (!$this->field ? '*' : $this->field));
        #处理order
        if ($this->order) {
            $sql->order($this->order);
        }
        #处理where
        if ($where) {
            $sql = $sql->where($where)->pager($this->page, $this->size)->select();
            $this->reset();
            return $this->db->queryAll($sql, $args);
        }
        $sql = $sql->pager($this->page, $this->size);
        $this->reset();
        return $this->db->queryAll($sql);
    }

    /**
     * 保存数据
     * @param array       $data
     * @param null|string $keyColumn 使用此项相当于change
     * @return bool|int
     */
    protected function save($data, $keyColumn = null) {
        if ($keyColumn) {
            #处理出数据给change方法用以修改数据
            $keyColumn = explode(',', $keyColumn);
            $where     = '';
            $arg       = array();
            foreach ($keyColumn as $key) {
                #加上w_区别与数据中的值，防止同名的占位符
                $where .= $key . ' = :w_' . $key;
                $arg['w_' . $key] = $data[$key];
                unset($data[$key]);
                if (isset($data[$key . '_new'])) {
                    #如果更新数据时有替换数据，修改键名为字段名称
                    $data[$key] = $data[$key . '_new'];
                    unset($data[$key . '_new']);
                }
            }
            return $this->change($data, $where, $arg);
        }
        $cols = join(',', array_keys($data));
        $sql  = $this->sql($this->table, $cols)->insert();
        return $this->db->exec($sql, $data);
    }

    /**
     * 修改数据
     * @param array  $data
     * @param string $where 自己注意WHERE条件的占位符不要和字段名重合
     * @param array  $args
     * @return bool|int
     */
    protected function change($data, $where, $args = array()) {
        $cols = join(',', array_keys($data));
        $sql  = $this->sql($this->table, $cols)->where($where)->update();
        $args = $data + $args;
        return $this->db->exec($sql, $args);
    }

    /**
     * 删除数据
     * @param string $keyValue 类似find方法
     * @param bool   $isKey
     * @return bool|int
     */
    protected function remove($keyValue, $isKey = true) {
        if (!$isKey) {
            $sql = $this->sql($this->table, (!$this->field ? '*' : $this->field))
                ->where($keyValue)->delete();
            return $this->db->exec($sql);
        }
        $sql = $this->sql($this->table, (!$this->field ? '*' : $this->field))
            ->where($this->primary . ' = :' . $this->primary)->delete();
        $this->reset();
        return $this->db->exec($sql, array($this->primary => $keyValue));
    }


}
