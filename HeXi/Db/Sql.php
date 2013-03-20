<?php

/**
 * SQL处理类
 * Class Sql
 */
class Sql {

    /**
     * 创建一个SQL对象
     * @param string $table 表格
     * @param string $cols 字段
     * @return Sql
     */
    public static function make($table, $cols = '*') {
        $cols = explode(',', $cols);
        return new Sql($table, $cols);
    }

    /**
     * 表格名称
     * @var string
     */
    private $table;

    /**
     * 字段
     * @var array
     */
    private $cols;

    /**
     * 构造函数
     * @param string $table
     * @param array $cols
     */
    private function __construct($table, $cols) {
        $this->table = $table;
        $this->cols = $cols;
    }

    /**
     * 插入语句
     * @return string
     */
    public function insert() {
        $sql = 'INSERT INTO ' . $this->table . '(';
        $sql .= join(',', $this->cols) . ') VALUES (';
        foreach ($this->cols as $col) {
            $sql .= ':' . $col . ',';
        }
        $sql = rtrim($sql, ',') . ')';
        return $sql;
    }

    /**
     * WHERE条件
     * @var array
     */
    private $where = array();

    /**
     * 添加WHERE条件
     * @param string $condition
     * @return Sql
     */
    public function where($condition) {
        $this->where[] = $condition;
        return $this;
    }

    /**
     * 拼接WHERE条件
     * @return string
     */
    private function buildWhere() {
        return join(' AND ', $this->where);
    }

    /**
     * LIMIT条件
     * @var string
     */
    private $limit;

    /**
     * 添加LIMIT条件
     * @param int $limit
     * @param int $offset
     * @return Sql
     */
    public function limit($limit, $offset = 0) {
        $this->limit = $limit . ($offset > 0 ? ' OFFSET ' . $offset : '');
        return $this;
    }

    /**
     * 添加分页的LIMIT条件
     * @param int $page
     * @param int $size
     * @return Sql
     */
    public function page($page, $size) {
        #计算分页的参数
        $this->limit = ($page - 1) * $size . ' , ' . $size;
        return $this;
    }

    /**
     * ORDER条件
     * @var string
     */
    private $order;

    /**
     * ORDER条件
     * @param string $order
     * @return Sql
     */
    public function order($order) {
        $this->order = $order;
        return $this;
    }

    /**
     * DELETE语句
     * @return string
     */
    public function delete() {
        $sql = 'DELETE FROM ' . $this->table;
        if ($this->where) {
            $sql .= ' WHERE ' . $this->buildWhere();
        }
        if ($this->order) {
            $sql .= ' ORDER BY ' . $this->order;
        }
        if ($this->limit) {
            $sql .= ' LIMIT ' . $this->limit;
        }
        return $sql;
    }

    /**
     * UPDATE语句
     * @return string
     */
    public function update() {
        $sql = 'UPDATE ' . $this->table . ' SET ';
        foreach ($this->cols as $col) {
            $sql .= '' . $col . ' = :'.$col.' ,';
        }
        $sql = rtrim($sql, ' ,');
        if ($this->where) {
            $sql .= ' WHERE ' . $this->buildWhere();
        }
        if ($this->order) {
            $sql .= ' ORDER BY ' . $this->order;
        }
        if ($this->limit) {
            $sql .= ' LIMIT ' . $this->limit;
        }
        return $sql;
    }

    //---------------------

    /**
     * JOIN条件
     * @var array
     */
    private $join;

    /**
     * LEFT JOIN
     * @param string $table
     * @param null|string $on
     * @return Sql
     */
    public function leftJoin($table, $on = null) {
        $this->join[] = array('left', $table, $on);
        return $this;
    }

    /**
     * RIGHT JOIN
     * @param string $table
     * @param null|string $on
     * @return Sql
     */
    public function rightJoin($table, $on = null) {
        $this->join[] = array('right', $table, $on);
        return $this;
    }

    /**
     * RIGHT JOIN
     * @param string $table
     * @param null|string $on
     * @return Sql
     */
    public function innerJoin($table, $on = null) {
        $this->join[] = array('inner', $table, $on);
        return $this;
    }

    /**
     * 拼接JOIN语句
     * @return string
     */
    private function buildJoin() {
        $sql = '';
        foreach ($this->join as $join) {
            $sql .= ' ' . strtoupper($join[0]) . ' JOIN ' . $join[1];
            if ($join[2]) {
                $sql .= ' ON ' . $join[2];
            }
        }
        return rtrim($sql);
    }

    /**
     * SELECT语句
     * @return string
     */
    public function select() {
        $sql = 'SELECT ' . join(',', $this->cols) . ' FROM ' . $this->table;
        if ($this->join) {
            $sql .= $this->buildJoin();
        }
        if ($this->where) {
            $sql .= ' WHERE ' . $this->buildWhere();
        }
        if ($this->order) {
            $sql .= ' ORDER BY ' . $this->order;
        }
        if ($this->limit) {
            $sql .= ' LIMIT ' . $this->limit;
        }
        return $sql;
    }

    //-----------------------

    /**
     * COUNT语句
     * @return string
     */
    public function count() {
        #处理count语句
        $sql = 'SELECT count(*) AS num FROM ' . $this->table;
        if ($this->where) {
            $sql .= ' WHERE ' . $this->buildWhere();
        }
        if ($this->order) {
            $sql .= ' ORDER BY ' . $this->order;
        }
        if ($this->limit) {
            $sql .= ' LIMIT ' . $this->limit;
        }
        return $sql;
    }

    //-----------------------------------------

    /**
     * SQL保存
     * @var array
     */
    private static $logSql = array();

    /**
     * 记录SQL
     * @param string $sql
     */
    public static function log($sql) {
        self::$logSql[] = $sql;
    }

    /**
     * 获取已经存储的SQL
     * @return array
     */
    public static function all() {
        return self::$logSql;
    }
}