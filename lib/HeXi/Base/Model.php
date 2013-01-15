<?php

/**
 * Model抽象类
 */
abstract class baseModel {

    /**
     * 数据加载状态
     * @var bool
     */
    private $_DbStatus = false;

    /**
     * SQL类加载状态
     * @var bool
     */
    private $_SqlStatus = false;

    /**
     * 调用数据库
     */
    private function enableDatabase() {
        HeXi::import('HeXi.Database.Db');
        $this->_DbStatus = true;
    }

    /**
     * 调用SQL类
     */
    private function enableSqlBuilder() {
        HeXi::import('HeXi.Database.Sql');
        $this->_SqlStatus = true;
    }

    /**
     * 获取SQL类
     * @param string $table
     * @param string $column
     * @return Sql
     */
    protected function sql($table, $column = '*') {
        if (!$this->_SqlStatus) {
            $this->enableSqlBuilder();
        }
        return Sql::table($table, $column);
    }

    /**
     * 获取数据库对象
     * @param string $name
     * @return AbstractDriver|PdoDriver
     */
    protected function useDb($name = 'default') {
        if (!$this->_DbStatus) {
            $this->enableDatabase();
        }
        return Db::connect($name);
    }

}
