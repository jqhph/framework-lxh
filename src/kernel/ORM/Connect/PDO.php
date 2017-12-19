<?php

namespace Lxh\ORM\Connect;

use Lxh\Exceptions\Exception;

class PDO
{
    /*
     * 成员属性
     */
    private $db_type;	//数据库类型
    private $host;		//主机名
    private $port;		//端口号
    private $user;		//用户名
    private $pass;		//密码
    private $charset;	//字符集
    private $dbname;	//数据库名称
    private $prefix;	//表前缀
    private $pdo;		//PDO实例化对象

    protected $options = [];

    protected $config;
    // 是否启用连接池
    protected $usepool;

    // 保存最后执行的sql语句
    public static $lastSql;

    public static $lastPrepareData = [];

    protected $stmt;

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
        $this->db_type = isset($config['type'])    ? $config['type']    : 'mysql';
        $this->host    = isset($config['host'])    ? $config['host']    : 'localhost';
        $this->port    = isset($config['port'])    ? $config['port']    : 3306;
        $this->user    = isset($config['user'])    ? $config['user']    : 'root';
        $this->pass    = isset($config['pwd'])     ? $config['pwd']     : '';
        $this->charset = isset($config['charset']) ? $config['charset'] : 'utf8';
        $this->dbname  = isset($config['name'])    ? $config['name']    : '';
        $this->prefix  = isset($config['prefix'])  ? $config['prefix']  : '';

        //连接数据库
        $this->connect();

        //设置编码
        $this->pdo->query('set names ' . $this->charset);
    }

    public function getPDO()
    {
        return $this->pdo;
    }

    /*
     * 连接数据库
     * 成功产生PDO对象,失败提示错误信息
     */
    private function connect()
    {
        $dsn = "{$this->db_type}:host={$this->host};port={$this->port};dbname={$this->dbname};charset={$this->charset}";

        $s = microtime(true);

        if ($this->usepool) {
            $this->pdo = new \pdoProxy($dsn, $this->user, $this->pass);
        } else {
            $this->pdo = new \PDO($dsn, $this->user, $this->pass);
        }

        // 记录追踪调试信息
        db_track($dsn, $s, 'c');

        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);//开启异常处理
        return $this->pdo;
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
        self::$lastSql = & $command;

//        if (! $this->check($command)) {
//            throw new \Exception('It is not safe to do this query', 0);
//        }

        $s = microtime(true);

        $res = $this->pdo->exec($command);

        // 记录追踪调试信息
        db_track($command, $s, 'w');

        $this->release();

        return $res;
    }

    // 获取单行数据
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


    // 批量添加
    public function batchAdd($table = '', array & $data, $replace = false)
    {
        $field  = '';
        $values = '';
        $key    = '';
        $vals   = '';

        $prepearData = [];

        foreach ($data as & $info) {
            if (empty($info))
                continue;

            foreach ($info as $k => & $v) {
                if ($key != 'ok')
                    $key  .= "`$k`,";
                $vals .= '?,';//$vals .= '"' . $v . '",';

                $prepearData[] = $v;
            }
            if (empty($field)) {
                $field  = substr($key,  0, - 1);
                $key    = 'ok';
            }
            $vals    = substr($vals,  0, - 1);
            $values .= '(' . $vals . '),';
            $vals    = '';
        }
        $values = substr($values, 0, -1);

        $pre = $replace ? 'REPLACE' : 'INSERT';

        $ignore = $this->option('ignore') ? 'IGNORE' : '';

        $sql = "$pre $ignore INTO `$table` ($field) VALUES $values";

        self::$lastSql = & $sql;

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
    public function prepare($sql, array & $data = [], $select = true)
    {
        self::$lastSql = & $sql;
        self::$lastPrepareData = & $data;

        $s = microtime(true);

        $this->stmt = $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);

        // 记录追踪调试信息
        db_track($sql, $s, 'unknown', $data);

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
     * @return stmt
     */
    public function query($sql)
    {
        self::$lastSql = & $sql;

        $s = microtime(true);

        $this->stmt = $this->pdo->query($sql);

        // 记录追踪调试信息
        db_track($sql, $s, 'r');

        return $this->stmt;
    }

    public function options(array $options)
    {
        $this->options = &$options;
        return $this;
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
        foreach ($data as $key => $val) {
            if (strpos($key, '=') === false) {
                $updateStr .= "`$key` = ?,";
            } else {
                $updateStr .= "$key ?,";
            }

            $data[] = $val;
            unset($data[$key]);
        }

        $updateStr = substr($updateStr, 0, - 1);

//        $ignore = $this->option('ignore') ? 'IGNORE' : '';

        $sql = "UPDATE `$table` SET {$updateStr} {$where}";

        $data = array_merge($data, $whereData);
        return $this->prepare($sql, $data, false);
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

        foreach ($data as $k => $v) {
            $field  .= "`$k`,";
            $values .= '?,';

            unset($data[$k]);
            $data[] = $v;
        }
        $field  = substr($field,  0, - 1);
        $values = substr($values, 0, - 1);

        $ignore = $this->option('ignore') ? 'IGNORE' : '';

        $sql = "INSERT $ignore INTO `$table` ($field) VALUES ($values)";

        $res = $this->prepare($sql, $data, false);
        $id = $this->pdo->lastInsertId();

        return $id ?: $res;
    }

    /**
     *
     *
     * @param string $k
     * @param string $def
     * @return mixed
     */
    public function option($k, $def = '')
    {
        return isset($this->options[$k]) ? $this->options[$k] : $def;
    }

    public function replace($table, array $data)
    {
        $field = '';
        $values = '';

        foreach ($data as $k => & $v) {
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

    public function delete($table, $where = '', array $whereData = [])
    {
        $sql = "DELETE FROM `$table` $where";
        return $this->prepare($sql, $whereData, false);
    }

}
