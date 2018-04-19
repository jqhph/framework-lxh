<?php

namespace Lxh\ORM\Connect;

use Lxh\Exceptions\Exception;

class PDO
{
    private $type;	    //数据库类型
    private $host;		//主机名
    private $port;		//端口号
    private $user;		//用户名
    private $pass;		//密码
    private $charset;	//字符集
    private $dbname;	//数据库名称
    private $prefix;	//表前缀
    private $breakReconnect;

    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * 是否启用连接池
     *
     * @var bool
     */
    protected $usepool;

    /**
     * 选项配置
     *
     * @var array
     */
    protected $options = [];

    /**
     * 保存最后执行的sql语句
     *
     * @var string
     */
    public static $lastSql;

    /**
     * @var float
     */
    protected $debugAt;

    /**
     * 最后执行sql的预处理绑定参数
     *
     * @var array
     */
    public static $lastPrepareData = [];

    /**
     * @var \PDOStatement
     */
    protected $stmt;

    /**
     * @var int
     */
    protected $transTimes = 0;

    /**
     * 构造方法 初始化数据库连接
     * @param array $arr   = array() 连接数据库信息数组
     * @param bool  $error = true    true开启异常处理模式,false关闭异常处理模式
     */
    public function __construct(array $config = [])
    {
        if (! $config) {
            throw new Exception('Lack of database configuration information.');
        }

        $this->usepool = isset($config['usepool']) ? $config['usepool'] : false;
        $this->type    = isset($config['type'])    ? $config['type']    : 'mysql';
        $this->host    = isset($config['host'])    ? $config['host']    : 'localhost';
        $this->port    = isset($config['port'])    ? $config['port']    : 3306;
        $this->user    = isset($config['user'])    ? $config['user']    : 'root';
        $this->pass    = isset($config['pwd'])     ? $config['pwd']     : '';
        $this->charset = isset($config['charset']) ? $config['charset'] : 'utf8';
        $this->dbname  = isset($config['name'])    ? $config['name']    : '';
        $this->prefix  = isset($config['prefix'])  ? $config['prefix']  : '';

        $this->breakReconnect = isset($config['breakReconnect'])  ? $config['breakReconnect']  : false;

        //连接数据库
        $this->connect();

        //设置编码
        $this->pdo->query('set names ' . $this->charset);
    }

    /*
     * 连接数据库
     * 成功产生PDO对象,失败提示错误信息
     */
    private function connect()
    {
        $dsn = "{$this->type}:host={$this->host};port={$this->port};dbname={$this->dbname};charset={$this->charset}";

        $this->debug($dsn);

        if ($this->usepool) {
            $this->pdo = new \pdoProxy($dsn, $this->user, $this->pass);
        } else {
            $this->pdo = new \PDO($dsn, $this->user, $this->pass);
        }

        // 记录追踪调试信息
        $this->debugEnd('c');

        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);//开启异常处理
        return $this->pdo;
    }

    public function options(array $options)
    {
        $this->options = &$options;
        return $this;
    }

    /**
     * 获取选项参数
     *
     * @param string $k
     * @param string $def
     * @return mixed
     */
    public function option($k, $def = '')
    {
        return isset($this->options[$k]) ? $this->options[$k] : $def;
    }

    /**
     * 开启连接池后需要调用此方法来释放这个进程占用的连接到池子里面;
     */
    public function release()
    {
        if ($this->usepool) {
            $this->pdo->release();
        }
    }
    //--------------------------------------------------------------
    // | 无预处理, 直接执行sql操作
    //--------------------------------------------------------------
    /**
     * exec写操作
     *
     */
    public function exec($command)
    {
        $this->debug($command);
        $res = $this->pdo->exec($command);
        $this->debugEnd('w');

        $this->release();

        return $res;
    }

    /**
     * 获取单行数据
     *
     * @return mixed
     */
    public function fetch()
    {
        return $this->stmt->fetch(\PDO::FETCH_ASSOC);
    }


    public function numRows()
    {
        return $this->stmt ? $this->stmt->fetchAll(\PDO::FETCH_NUM) : false;
    }

    public function numFields()
    {
        return $this->stmt ? $this->stmt->fetchAll(\PDO::FETCH_COLUMN) : false;
    }

    /**
     * SQL性能分析
     *
     * @param string $sql
     * @return array
     */
    public function getExplain($sql)
    {
        $sql = "EXPLAIN $sql";

        return array_change_key_case($this->one($sql));
    }

    /**
     * @param $sql
     * @param array $params
     */
    protected function debug(&$sql, $params = [])
    {
        self::$lastSql         = &$sql;
        self::$lastPrepareData = &$params;
        $this->debugAt         = microtime(true);
    }

    /**
     * 调试结束
     */
    protected function debugEnd($type = 'r')
    {
        // 记录追踪调试信息
        db_track(self::$lastSql, $this->debugAt, $type, self::$lastPrepareData);
    }


    /**
     * 取得数据库的表信息
     *
     * @param string $dbName
     * @return array
     */
    public function getTables($dbName = null)
    {
        $dbName = $dbName === false ? '' : ($dbName ?: $this->dbname);

        $sql = !empty($dbName) ? 'SHOW TABLES FROM ' . $dbName : 'SHOW TABLES ';

        $info   = [];
        foreach ($this->all($sql) as $key => &$val) {
            $info[$key] = current($val);
        }
        return $info;
    }

    /**
     * 取得数据表的字段信息
     * 
     * @param string $table
     * @return array
     */
    public function getFields($table)
    {
        list($table) = explode(' ', $table);
        if (false === strpos($table, '`')) {
            if (strpos($table, '.')) {
                $table = str_replace('.', '`.`', $table);
            }
            $table = '`' . $table . '`';
        }
        $sql = 'SHOW COLUMNS FROM ' . $table;

        $result = $this->all($sql);

        $info   = [];
        foreach ($result as $key => &$val) {
            $val                 = array_change_key_case($val);
            $info[$val['field']] = [
                'name'    => $val['field'],
                'type'    => $val['type'],
                'notnull' => (bool) ('' === $val['null']), // not null is empty, null is yes
                'default' => $val['default'],
                'primary' => (strtolower($val['key']) == 'pri'),
                'autoinc' => (strtolower($val['extra']) == 'auto_increment'),
            ];
        }

        return $info;
    }

    /**
     * 执行数据库事务
     *
     * @param callable $callback 数据操作方法回调
     * @return mixed
     * @throws \PDOException
     * @throws \Exception
     * @throws \Throwable
     */
    public function transaction($callback)
    {
        $this->startTrans();
        try {
            $result = null;
            if (is_callable($callback)) {
                $result = call_user_func_array($callback, [$this]);
            }
            $this->commit();
            return $result;
        } catch (\Exception $e) {
            $this->rollback();
            throw $e;
        } catch (\Throwable $e) {
            $this->rollback();
            throw $e;
        }
    }


    /**
     * 用于非自动提交状态下面的查询提交
     *
     * @return $this
     * @throws \PDOException
     */
    public function commit()
    {
        if (1 == $this->transTimes) {
            $this->pdo->commit();
        }

        --$this->transTimes;
        return $this;
    }

    /**
     * 事务回滚
     *
     * @return $this
     * @throws \PDOException
     */
    public function rollback()
    {
        if (1 == $this->transTimes) {
            $this->pdo->rollBack();
        } elseif ($this->transTimes > 1 && $this->supportSavepoint()) {
            $this->pdo->exec(
                $this->parseSavepointRollBack('trans' . $this->transTimes)
            );
        }

        $this->transTimes = max(0, $this->transTimes - 1);
        return $this;
    }

    /**
     * 启动事务
     *
     * @return void
     */
    public function startTrans()
    {
        ++$this->transTimes;
        try {
            if (1 == $this->transTimes) {
                $this->pdo->beginTransaction();
            } elseif ($this->transTimes > 1 && $this->supportSavepoint()) {
                $this->pdo->exec(
                    $this->parseSavepoint('trans' . $this->transTimes)
                );
            }

        } catch (\PDOException $e) {
            if ($this->isBreak($e)) {
                return $this->close()->startTrans();
            }
            throw $e;
        } catch (\ErrorException $e) {
            if ($this->isBreak($e)) {
                return $this->close()->startTrans();
            }
            throw $e;
        }
    }

    /**
     * 是否断线
     *
     * @param \PDOException  $e 异常对象
     * @return bool
     */
    public function isBreak($e)
    {
        if (!$this->breakReconnect) {
            return false;
        }

        $info = [
            'server has gone away',
            'no connection to the server',
            'Lost connection',
            'is dead or not enabled',
            'Error while sending',
            'decryption failed or bad record mac',
            'server closed the connection unexpectedly',
            'SSL connection has been closed unexpectedly',
            'Error writing data to the connection',
            'Resource deadlock avoided',
        ];

        $error = $e->getMessage();

        foreach ($info as $msg) {
            if (false !== stripos($error, $msg)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 释放查询结果
     *
     * @return $this
     */
    public function free()
    {
        $this->stmt = null;
        return $this;
    }

    /**
     * 关闭数据库（或者重新连接）
     * @access public
     * @return $this
     */
    public function close()
    {
        $this->pdo  = null;
        $this->stmt = null;

        return $this;
    }

    /**
     * 生成定义保存点的SQL
     *
     * @param $name
     * @return string
     */
    protected function parseSavepoint($name)
    {
        return 'SAVEPOINT ' . $name;
    }

    /**
     * 生成回滚到保存点的SQL
     *
     * @param $name
     * @return string
     */
    protected function parseSavepointRollBack($name)
    {
        return 'ROLLBACK TO SAVEPOINT ' . $name;
    }

    protected function supportSavepoint()
    {
        return true;
    }

    // 批量添加
    public function batchAdd($table = '', array &$data, $replace = false)
    {
        $field  = '';
        $values = '';
        $key    = '';
        $vals   = '';

        $prepearData = [];

        foreach ($data as &$info) {
            if (empty($info))
                continue;

            foreach ($info as $k => &$v) {
                if (! $k) continue;
                if ($key !== true)
                    $key  .= "`$k`,";
                $vals .= '?,';

                $prepearData[] = $v;
            }
            if (empty($field)) {
                $field = substr($key,  0, - 1);
                $key   = true;
            }
            $vals    = substr($vals,  0, - 1);
            $values .= '(' . $vals . '),';
            $vals    = '';
        }
        $values = substr($values, 0, -1);

        $pre = $replace ? 'REPLACE' : 'INSERT';

        $ignore = $this->option('ignore') ? 'IGNORE' : '';

        $sql = "$pre $ignore INTO `$table` ($field) VALUES $values";

        $res = $this->prepare($sql, $prepearData, false);
        $id  = $this->pdo->lastInsertId();

        $this->release();

        return $id ?: $res;
    }


    //--------------------------------------------------------------
    // | 预处理执行sql操作
    //--------------------------------------------------------------

    /**
     * 预处理sql语句
     *
     * @param  $sql string
     * @param  $data array 预处理绑定参数数组
     * @param  $select bool 是否为查询操作，默认true
     * @return mixed
     */
    public function prepare($sql, array &$data = [], $select = true)
    {
        $this->debug($sql, $data);
        $this->stmt = $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
        $this->debugEnd('unknown');

        $this->release();

        if ($select) {
            return $stmt;
        }

        return $stmt->rowCount();
    }

    /**
     * 获取原始pdo对象
     *
     * @return \PDO
     */
    public function resource()
    {
        return $this->pdo;
    }

    /**
     * 执行SQL语句，非写操作
     *
     * @param  $sql string
     * @return \PDOStatement
     */
    public function query($sql)
    {
        $this->debug($sql);
        $this->stmt = $this->pdo->query($sql);
        $this->debugEnd();

        return $this->stmt;
    }

    /**
     * 查询单条数据操作
     *
     * @param  string $sql 要处理的SQL语句
     * @param array $whereData where字句值, 如: [48, '小强']
     * @return mixed       成功返回关联一维数组,失败返回空数组
     */
    public function one($sql, array $whereData = [])
    {
        $stmt = $this->prepare($sql, $whereData);

        return $stmt ? $stmt->fetch(\PDO::FETCH_ASSOC) : [];
    }

    /**
     * 查询多条数据操作
     *
     * @param  string $sql 要处理的SQL语句
     * @param array $whereData where字句值, 如: [48, '小强']
     * @return mixed       成功返回关联二维数组,失败返回空数组
     */
    public function all($sql, array $whereData = [])
    {
        $stmt = $this->prepare($sql, $whereData);

        return $stmt ? $stmt->fetchAll(\PDO::FETCH_ASSOC) : [];
    }

    /**
     * 预处理修改
     *
     * @param string $table
     * @param array $data 要修改的数据
     * @param string $where where字句, 参数值用"?"代替, 如: WHERE id = ? AND name = ?
     * @param array $whereData where字句值, 如: [48, '小强']
     * @return int
     */
    public function update($table, array $data = [], $where = '', array $whereData = [])
    {
        $updateStr = '';
        $values = [];
        foreach ($data as $key => $val) {
            if (! $key) continue;
            if (strpos($key, '=') === false) {
                $updateStr .= "`$key` = ?,";
            } else {
                $updateStr .= "$key ?,";
            }

            $values[] = $val;
        }

        $updateStr = substr($updateStr, 0, - 1);

//        $ignore = $this->option('ignore') ? 'IGNORE' : '';

        $sql = "UPDATE `$table` SET {$updateStr} {$where}";

        $values = array_merge($values, $whereData);
        return $this->prepare($sql, $values, false);
    }

    /**
     * 获取查询结果的id数组
     *
     * @return array
     */
    public function fetchIds($sql, array $data = [], $idKey = 'id')
    {
        $this->prepare($sql, $data);
        $ids = array();
        while ($row = $this->fetch()) {
            $ids[] = $row[$idKey];
        }
        return $ids;
    }

    /**
     * 获取以id为key的二维数组查询结果
     *
     * @return array
     */
    public function fetchIdRows($sql, array $data = [], $idKey = 'id')
    {
        $this->prepare($sql, $data);
        $rows = array();
        while ($row = $this->fetch()) {
            $rows[$row[$idKey]] = $row;
        }
        return $rows;
    }

    /**
     * 获取预处理where字句in字符串
     *
     * @param  array $ins where字句in数组值
     * @return string
     */
    public function normalizePrepareIn(array $ins)
    {
        $instr = '';
        foreach ($ins as & $in) {
            $instr .= '?,';
        }
        return rtrim($instr, ',');
    }


    /**
     * 添加数据
     *
     * @param string $table
     * @param array $data
     * @return boolean
     */
    public function add($table, array $data)
    {
        $field = '';
        $values = '';

        $inserts = [];
        foreach ($data as $k => $v) {
            if (! $k) continue;
            $field  .= "`$k`,";
            $values .= '?,';

            $inserts[] = $v;
        }
        $field  = substr($field,  0, - 1);
        $values = substr($values, 0, - 1);

        $ignore = $this->option('ignore') ? 'IGNORE' : '';

        $sql = "INSERT $ignore INTO `$table` ($field) VALUES ($values)";

        $res = $this->prepare($sql, $inserts, false);
        $id = $this->pdo->lastInsertId();

        return $id ?: $res;
    }

    public function replace($table, array $data)
    {
        $field = '';
        $values = '';

        foreach ($data as $k => $v) {
            $field  .= "`$k`,";
            $values .= '?,';

            unset($data[$k]);
            $data[] = $v;
        }
        $field  = substr($field,  0, - 1);
        $values = substr($values, 0, - 1);

        $sql = "REPLACE INTO `$table` ($field) VALUES ($values)";

        $res = $this->prepare($sql, $data, false);
        $id = $this->pdo->lastInsertId();

        return $id ?: $res;
    }

    /**
     * 删除操作
     *
     * @param $table
     * @param string $where
     * @param array $whereData
     * @return mixed
     */
    public function delete($table, $where = '', array $whereData = [])
    {
        $sql = "DELETE FROM `$table` $where";
        return $this->prepare($sql, $whereData, false);
    }

    public function __destruct()
    {
        $this->close();
    }

}
