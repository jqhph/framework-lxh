<?php
namespace Lxh\ORM\Connect;

class Redis 
{
	private static $host;
	private static $port;
	private static $pwd;
	private static $db;
	/**
	 * @var \Redis
	 */
	private $redis;
	
	protected $usepool;
	
	# 是否开启debug模式
	protected $debug;
	
	
	public function __construct(array $conf = []) 
	{
		$conf = config('db.redis');
		
		$this->debug = getvalue($conf, 'debug');
		
		$this->usepool = getvalue($conf, 'usepool', false);
		self::$host    = getvalue($conf, 'host');
		self::$port    = getvalue($conf, 'port');
		self::$pwd     = getvalue($conf, 'pwd');
		self::$db      = getvalue($conf, 'db');
		
		$this->connect();
		$this->auth();//验证用户名
		$this->select();
		
	}

	public function resource()
	{
		return $this->redis;
	}
	
	/**
	 * 开启连接池后需要调用此方法来释放这个进程占用的连接到池子里面;
	 * */
	public function release()
	{
		if ($this->usepool) {
			$this->redis->release();
		}
	}
	
	/**
	 * 记录debug信息
	 *
	 * @param int $start 开始时间
	 * @param string $sql sql语句
	 * @param string $type r|w|c 读写和连接数据库操作
	 * */
	protected function debug(&$start, $type = 'r')
	{
		if (! $this->debug) {
			return;
		}
		
		track('cache.s', [
			'run'  => microtime(true) - $start,
			'type' => $type
		]);
		track('cache.' . $type);
	}

	////Redis server went away  Connection closed
	private function connect() 
	{
		$start = microtime(true);
		if ($this->usepool) {
			$this->redis = new \redisProxy();
		} else {
			$this->redis = new \Redis();
		}
		$this->redis->connect(self::$host, self::$port, 0);
		$this->debug($start, 'c');
	}
	
	private function auth() 
	{
		$this->redis->auth(self::$pwd);
	
	}
	public function select($index = false) 
	{
		if ($index !== false) {
			$this->redis->select($index);
		} else {
			$this->redis->select(self::$db);
		}
	
	}
	
	//设置字符串值
	//@param  timeout缓存时间（秒）
	public function set($key, $value = null, $timeout = null) 
	{
		$start = microtime(true);
		if ($timeout === null)
			$res = $this->redis->set($key, $value);
		else 
			$res = $this->redis->setex($key, $timeout, $value);//key存在则替换原来的值 TTL方法获取剩余缓存时间
		
		$this->debug($start, 'w');
		return $res;
	}

	/**
	 * 正则匹配key
	 *
	 * @param $where
	 * @return array
	 */
	public function keys($where) 
	{
		$start = microtime(true);
		$res = $this->redis->keys($where);
		$this->debug($start);
		return $res;
	}

	/**
	 * 自增一
	 *
	 * @param $key
	 * @return int
	 */
	public function incr($key) 
	{
		$start = microtime(true);
		$res = $this->redis->incr($key);
		$this->debug($start, 'w');
		return $res;
	}


	/**
	 * 获取字符串
	 *
	 * @param $keys
	 * @return bool|string
	 */
	public function get($keys) 
	{
		$start = microtime(true);
		$res = $this->redis->get($keys);
		$this->debug($start);
		return $res;
	}
	
	
	public function expire($key, $second = 1) {//默认一秒后过期
		$this->redis->expire($key, $second);
	}
	
	public function del($key) 
	{
		$start = microtime(true);
		$res = $this->redis->del($key);
		$this->debug($start, 'w');
		return $res;
	}
	
	//****************************哈希操作
	
	//指定值+= $num即为要增加的整数，如果是减少则传负数即可
	public function hIncrby($key, $field, $num) 
	{
		$start = microtime(true);
		$res = $this->redis->hIncrBy($key, $field, $num);
		$this->debug($start, 'w');
		return $res;
	}
	
	//批量插入哈希值
	public function hMset($key, $data) 
	{
		$start = microtime(true);
		$res = $this->redis->hMset($key, $data);
		$this->debug($start, 'w');
		return $res;
	}
	
	public function hMget($key, $fields) 
	{
		$start = microtime(true);
		$res = $this->redis->hMget($key, $fields);
		$this->debug($start);
		return $res;
	}
	
	public function hGetAll($key) 
	{
		$start = microtime(true);
		$res = $this->redis->hGetAll($key);
		$this->debug($start);
		return $res;
	}
	
	
	//获取hash表中的单个field方法
	public function hGet($key, $field) 
	{
		$start = microtime(true);
		$res = $this->redis->hGet($key, $field);
		$this->debug($start);
		return $res;
	}
	//给一个field存在则覆盖
	public function hSet($key, $field, $value) 
	{
		$start = microtime(true);
		$res = $this->redis->hSet($key, $field, $value);
		$this->debug($start, 'w');
		return $res;
	}
	
	//******************************队列操作  lSet修改指定位置的值    lget返回指定位置的值  llen返回队列长度
	public function rpush($key, $val) //往队列后边添加元素（右边），成功返回队列长度
	{
		$start = microtime(true);
		$res = $this->redis->rPush($key, $val);
		$this->debug($start, 'w');
		return $res;
	}
	
	public function lSet($key, $posi, $val) 
	{
		$start = microtime(true);
		$res = $this->redis->lSet($key, $val);
		$this->debug($start, 'w');
		return $res;
		
	}
	
	public function lpop($key) 
	{
		# 移出并返回队列的头元素
		$start = microtime(true);
		$res = $this->redis->lPop($key);
		$this->debug($start, 'w');
		return $res;
	}
	
	public function lRange($key, $start, $stop) 
	{
		# 返回指定位置的队列元素，如：key, 0, 4返回下标为0到4的队列值
		$startAt = microtime(true);
		$res = $this->redis->lrange($key, $start, $stop);
		$this->debug($startAt, 'w');
		return $res;
	}
	
	public function lRemove($key, $val)
	{
		//删除指定值
		$start = microtime(true);
		$res = $this->redis->lRemove($key, $val);
		$this->debug($start, 'w');
		return $res;
	}
	
	public function close() 
	{
		$res = $this->redis->close();
		$this->redis = null;
		return $res;
	}
}
