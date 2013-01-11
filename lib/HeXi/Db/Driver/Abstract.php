<?php
/**
 * Created by JetBrains PhpStorm.
 * User: FuXiaoHei
 * Date: 13-1-8
 * Time: 下午3:58
 * To change this template use File | Settings | File Templates.
 */
abstract class AbstractDriver {

    /**
     * 配置信息
     * @var array
     */
    protected $config;

    /**
     * 数据库的标示名称
     * @var string
     */
    protected $named;

    /**
     * 构造方法
     * @param string $name 数据库标识名称
     */
    public function __construct($name = 'default') {
        $this->config = $GLOBALS['config']['database'][$name];
        $this->named  = $name;
        if (!$this->config) {
            exit('No Database Connection named "' . $name . '" !');
        }
    }

    /**
     * 执行SQL
     * @param string $sql
     * @return int|bool
     */
    abstract public function exec($sql);

    /**
     * 查询SQL
     * @param string $sql
     * @return bool|null|array|object
     */
    abstract public function query($sql);

    /**
     * 查询一组数据
     * @param string $sql
     * @return array|null
     */
    abstract public function queryAll($sql);

    /**
     * 抛出错误
     */
    abstract public function error();

    /**
     * 最新添加的id
     * @return int
     */
    abstract public function lastId();

    /**
     * 影响的行数
     * @return int
     */
    abstract public function affectRows();
}
