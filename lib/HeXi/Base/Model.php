<?php
/**
 * Created by JetBrains PhpStorm.
 * User: FuXiaoHei
 * Date: 13-1-8
 * Time: 下午9:54
 * To change this template use File | Settings | File Templates.
 */
abstract class BaseModel extends BaseClass {

    /**
     * @var AbstractDriver|PdoDriver
     */
    protected $db;

    protected $table;

    protected $primary;

    protected $columns;

    public function __construct() {
        parent::__construct();
        $this->init();
        $this->activeData = array();
        $this->columns    = '*';
    }

    protected $activeData;

    abstract protected function init();

    public function __set($key, $value) {
        $this->activeData[$key] = $value;
        return $this;
    }

    protected function sql($table, $column = '*') {
        return Sql::table($table, $column);
    }

    protected function save($keys = null) {
        if (!$this->db) {
            exit('No Database Connection in Model');
        }
        $keys   = is_null($keys) ? array() : (is_string($keys) ? explode(',', $keys) : $keys);
        $column = array_diff(array_keys($this->activeData), $keys);
        $sql    = $this->sql($this->table, join(',', $column));
        if ($keys) {
            foreach ($keys as $k) {
                $sql->where($k . ' = :' . $k);
            }
            $sql = $sql->update();
        } else {
            $sql = $sql->insert();
        }
        $data             = $this->activeData;
        $this->activeData = array();
        return $this->db->exec($sql, $data);
    }

    protected function find($key) {
        $column        = $this->columns;
        $this->columns = '*';
        $sql           = $this->sql($this->table, $column)
            ->where($this->primary . ' = :' . $this->primary)
            ->select();
        return $this->db->query($sql, array($this->primary => $key));
    }

    protected function findAll($condition) {
        $condition = is_string($condition) ? array($condition) : $condition;
        $sql       = $this->sql($this->table, $this->columns);
        if ($condition['order']) {
            $sql->order($condition['order']);
        }
        if ($condition['limit']) {
            $sql->limit($condition['limit']);
        }
        if ($condition['pager']) {
            $sql->pager($condition['pager'][0], $condition['pager'][1]);
        }
        $where = isset($condition['where']) ? $condition['where'] : $condition;
        foreach ($where as $w) {
            $sql->where($w);
        }
        $sql              = $sql->select();
        $data             = $this->db->queryAll($sql, $this->activeData);
        $this->columns    = '*';
        $this->activeData = array();
        return $data;
    }

    public function delete($key = null) {
        $sql   = $this->sql($this->table);
        $where = array();
        if ($key) {
            $sql->where($this->primary . ' = :' . $this->primary);
            $where[$this->primary] = $key;
        }
        foreach ($this->activeData as $name => $value) {
            $sql->where($name . ' = :' . $name);
            $where[$name] = $value;
        }
        $this->activeData = array();
        return $this->db->exec($sql->delete(), $where);
    }

}
