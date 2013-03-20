<?php

/**
 * PDO数据库驱动类
 * Class PDODriver
 */
class PDODriver extends DbDriver {

    /**
     * 数据库对象
     * @var PDO
     */
    private $pdo;

    /**
     * 数据集对象
     * @var PDOStatement
     */
    private $stmt;

    /**
     * PDO连接的类型
     * @var string
     */
    public $pdoType;

    /**
     * 构造函数
     * @param array $config 配置信息
     * @throws Exception 连接时抛出异常
     */
    public function __construct($config) {
        parent::__construct($config);
        $this->pdoType = strtoupper($this->config['type']);
        #根据数据库类型生成PDO对象
        switch (strtolower($this->config['type'])) {
            case 'sqlite':
                return $this->connectSQLite();
            case 'mysql':
                return $this->connectMySQL();
        }
        throw new Exception('数据库类型不支持');
    }

    /**
     * 连接到SQLite
     * @return bool
     */
    private function connectSQLite() {
        $dsn = 'sqlite:' . $this->config['file'];
        $options = array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
        );
        $this->pdo = new PDO($dsn, null, null, $options);
        return true;
    }

    /**
     * 连接到MySQL
     * @return bool
     */
    private function connectMySQL() {
        $dsn = 'mysql:host=' . $this->config['host'] . ';port=' . $this->config['port'] . ';dbname=' . $this->config['dbname'];
        #MySQL需要处理编码问题
        $options = array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::MYSQL_ATTR_INIT_COMMAND => 'set names ' . $this->config['charset']
        );
        $this->pdo = new PDO($dsn, $this->config['user'], $this->config['password'], $options);
        return true;
    }

    /**
     * 数据类型绑定
     * @var array
     */
    protected $paramType = array(
        'bool' => PDO::PARAM_BOOL,
        'integer' => PDO::PARAM_INT,
        'float' => PDO::PARAM_STR,
        'string' => PDO::PARAM_STR,
        'lob' => PDO::PARAM_LOB,
        'binary' => PDO::PARAM_LOB,
        'null' => PDO::PARAM_NULL,
        'other' => PDO::PARAM_STR
    );

    /**
     * 获取数据类型
     * @param mixed $value
     * @return int
     */
    private function getDataType($value) {
        $type = gettype($value);
        $type = $this->paramType[$type];
        return !$type ? $this->paramType['other'] : $type;
    }

    /**
     * 执行SQL
     * @param string $sql
     * @param array $param 绑定参数
     * @return bool|int
     */
    public function exec($sql, $param = array()) {
        Sql::log($sql);
        if ($param) {
            $stmt = $this->pdo->prepare($sql);
            foreach ($param as $name => $value) {
                #按照类型绑定数据
                $stmt->bindValue(':' . $name, $value, $this->getDataType($value));
            }
            return $stmt->execute();
        }
        return $this->pdo->exec($sql);
    }

    /**
     * 查询SQL
     * @param string $sql
     * @param array $param
     * @param null|string $fetchClass
     * @return mixed
     */
    public function query($sql, $param = array(), $fetchClass = null) {
        Sql::log($sql);
        if ($param) {
            $this->stmt = $this->pdo->prepare($sql);
            foreach ($param as $name => $value) {
                $this->stmt->bindValue(':' . $name, $value, $this->getDataType($value));
            }
            $this->stmt->execute();
            #加载和返回需要序列化的类
            if ($fetchClass) {
                Hx::import(Hx::$name . '/Lib/Store/' . $fetchClass . 'Store');
                $this->stmt->setFetchMode(PDO::FETCH_CLASS, $fetchClass . 'Store');
                return $this->stmt->fetch();
            }
            $this->stmt->setFetchMode(PDO::FETCH_OBJ);
            return $this->stmt->fetch();
        }
        $this->stmt = $this->pdo->query($sql);
        if ($fetchClass) {
            Hx::import(Hx::$name . '/Lib/Store/' . $fetchClass . 'Store');
            $this->stmt->setFetchMode(PDO::FETCH_CLASS, $fetchClass . 'Store');
            return $this->stmt->fetch();
        }
        $this->stmt->setFetchMode(PDO::FETCH_OBJ);
        return $this->stmt->fetch();
    }

    /**
     * 查询一组结果
     * @param string $sql
     * @param array $param
     * @param null|string $fetchClass
     * @return array
     */
    public function queryAll($sql, $param = array(), $fetchClass = null) {
        Sql::log($sql);
        if ($param) {
            $this->stmt = $this->pdo->prepare($sql);
            foreach ($param as $name => $value) {
                $this->stmt->bindValue(':' . $name, $value, $this->getDataType($value));
            }
            $this->stmt->execute();
            if ($fetchClass) {
                Hx::import(Hx::$name . '/Lib/Store/' . $fetchClass . 'Store');
                return $this->stmt->fetchAll(PDO::FETCH_CLASS, $fetchClass . 'Store');
            }
            return $this->stmt->fetchAll();
        }
        $this->stmt = $this->pdo->query($sql);
        if ($fetchClass) {
            Hx::import(Hx::$name . '/Lib/Store/' . $fetchClass . 'Store');
            return $this->stmt->fetchAll(PDO::FETCH_CLASS, $fetchClass . 'Store');
        }
        return $this->stmt->fetchAll();
    }

    /**
     * 最后添加的ID
     * @return string
     */
    public function lastId() {
        return $this->pdo->lastInsertId();
    }

    /**
     * 统计数据集
     * @return int
     */
    public function rowCount() {
        return !$this->stmt ? 0 : $this->stmt->rowCount();
    }

    /**
     * 开启事务
     * @return bool
     */
    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }

    /**
     * 执行事务
     * @return bool
     */
    public function commit() {
        return $this->pdo->commit();
    }

    /**
     * 回滚事务
     * @return bool
     */
    public function rollback() {
        return $this->pdo->rollBack();
    }


}