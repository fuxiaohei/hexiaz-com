<?php

class Sql {
    /**
     * 从表名创建一个SQL生成类
     * @param string $tableName 表名称
     * @param string $column    字段名称
     * @return Sql
     */
    public static function table($tableName, $column = '*') {
        return new Sql($tableName, $column);
    }

    /**
     * 记录生成的SQL
     * @var array
     */
    protected static $logs = array();

    /**
     * 记录SQL语句
     * @param string $string
     */
    public static function log($string) {
        self::$logs[] = $string;
    }

    /**
     * 返回所有记录下的SQL
     * @return array
     */
    public static function all() {
        return self::$logs;
    }

    /**
     * 返回最后一条的SQL
     * @return string
     */
    public static function last() {
        return end(self::$logs);
    }

    /**
     * 构造方法
     * @param string $table  表格名称
     * @param string $column 字段名称
     */
    private function __construct($table, $column) {
        $this->table  = $table;
        $this->column = $column;
    }


    /**
     * 表格名称
     * @var string
     */
    protected $table;

    /**
     * 字段名称
     * @var string
     */
    protected $column;

    /**
     * WHERE条件
     * @var array
     */
    protected $where = array();

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
     * OR_WHERE条件
     * @var array
     */
    protected $orWhere = array();

    /**
     * 添加OR_WHERE条件
     * @param string $condition
     * @return Sql
     */
    public function orWhere($condition) {
        $this->orWhere[] = $condition;
        return $this;
    }

    /**
     * JOIN条件
     * @var array
     */
    protected $join = array();

    /**
     * 添加JOIN条件
     * @param string      $table
     * @param bool|string $on
     * @return Sql
     */
    public function join($table, $on = false) {
        $this->join['table'] = $table;
        $this->join['on']    = $on;
        return $this;
    }

    /**
     * GROUP BY条件
     * @var array
     */
    protected $group = array();

    /**
     * 添加GROUP BY条件
     * @param string      $column
     * @param bool|string $having
     * @return Sql
     */
    public function group($column, $having = false) {
        $this->group['column'] = $column;
        $this->group['having'] = $having;
        return $this;
    }

    /**
     * ORDER BY条件
     * @var null|string
     */
    protected $order = null;

    /**
     * 添加ORDER BY条件
     * @param string $order
     * @return Sql
     */
    public function order($order) {
        $this->order = $order;
        return $this;
    }

    /**
     * LIMIT条件
     * @var string
     */
    protected $limit;

    /**
     * 添加LIMIT条件
     * @param int $limit
     * @param int $offset
     * @return Sql
     */
    public function limit($limit, $offset = 0) {
        if ($offset) {
            $this->limit = $limit . ' OFFSET ' . $offset;
        } else {
            $this->limit = $limit;
        }
        return $this;
    }

    /**
     * 添加分页条件
     * @param int $page
     * @param int $size
     * @return Sql
     */
    public function pager($page, $size) {
        $this->limit = ($page - 1) * $size . ',' . $size;
        return $this;
    }

    /**
     * 返回INSERT语句
     * @return string
     */
    public function insert() {
        $sql  = "INSERT INTO {$this->table}($this->column) VALUES (";
        $temp = explode(',', $this->column);
        $sql .= ':' . join(',:', $temp) . ')';
        return $sql;
    }

    /**
     * 返回DELETE语句
     * @return string
     */
    public function delete() {
        $sql = "DELETE FROM {$this->table}";
        if ($this->where) {
            $sql .= $this->_buildWhere();
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
     * 拼接WHERE语句
     * @return string
     */
    private function _buildWhere() {
        $sql = '';
        if ($this->where || $this->orWhere) {
            $sql .= ' WHERE ';
            if ($this->where) {
                $sql .= join(' AND ', $this->where);
            }
            if ($this->orWhere) {
                if ($sql != ' WHERE ') {
                    $sql .= ' OR ';
                }
                $sql .= join(' AND ', $this->orWhere);
            }
        }
        return $sql;
    }

    /**
     * 添加SELECT语句
     * @return string
     */
    public function select() {
        $sql = "SELECT {$this->column} FROM {$this->table}";
        if ($this->join) {
            $sql .= ' JOIN ' . $this->join['table'];
            if ($this->join['on']) {
                $sql .= ' ON ' . $this->join['on'];
            }
        }
        $sql .= $this->_buildWhere();
        if ($this->group) {
            $sql .= ' GROUP BY ' . $this->group['column'];
            if ($this->group['having']) {
                $sql = ' HAVING ' . $this->group['having'];
            }
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
        $sql  = "UPDATE {$this->table} SET ";
        $temp = explode(',', $this->column);
        foreach ($temp as $v) {
            $sql .= $v . ' = :' . $v . ', ';
        }
        $sql = rtrim($sql, ', ');
        $sql .= $this->_buildWhere();
        if ($this->order) {
            $sql .= ' ORDER BY ' . $this->order;
        }
        if ($this->limit) {
            $sql .= ' LIMIT ' . $this->limit;
        }
        return $sql;
    }

    /**
     * 直接返回字符串时，返回SELECT语句
     * @return string
     */
    public function __toString() {
        return $this->select();
    }
}
