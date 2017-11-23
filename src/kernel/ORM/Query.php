<?php
namespace Lxh\ORM;

use Lxh\ORM\Connect\PDO;
use Lxh\ORM\Driver\BuilderManager;
use Lxh\Contracts\Container\Container;
use Lxh\ORM\Driver\Mysql\Builder;


/**
 * query builder
 */
class Query
{
	/**
	 * @var Container
	 */
	protected $container;

	/**
	 * 构造器
	 *
	 * @var mixed
	 */
	protected $builder;

	/**
	 * 数据库连接实例
	 */
	protected $connection;

	protected $connectionName = 'primary';

	protected $defaultConnectionType = 'primary';

	public function __construct(Container $container)
	{
		$this->builder = new Builder($container, $this);

		$this->container = $container;
	}

	public function setConnectionName($name)
	{
		$this->connectionName = $name;

		return $this;
	}

	/**
	 * 设置或获取数据库连接实例
	 *
	 * @return PDO
	 */
	public function connection($name = null)
	{
		$this->connectionName = $name ?: $this->connectionName;
		if ($this->connectionName && ! $this->connection) {
			$this->connection = pdo($this->connectionName);
		}
		if ($this->connection) {
			return $this->connection;
		}

		return $this->connection = pdo($this->defaultConnectionType);
	}

	/**
	 * 多对多关联(不支持AS别名)
	 *
	 * @param $mid string 中间表表名
	 * @param $relate string 要关联的表
	 *
	 * 表结构 up      --- id
	 *     user    --- id
	 *     user_up --- user_id, up_id
	->from('up')
	->manyToMany('user_up', 'user')
	相当于:

	->from('up')
	->leftJoin('user_up', 'id', 'user_up.up_id')
	->leftJoin('user', '`user`.id', 'user_up.user_id')

	SELECT * FROM `up`
	LEFT JOIN `user_up` ON `user_up`.up_id = `up`.`id`
	LEFT JOIN `user` ON `user_up`.user_id = `user`.id
	 *
	 * @return static
	 */
	public function manyToMany($mid, $relate)
	{
		$this->builder->manyToMany($mid, $relate);
		return $this;
	}

	/**
	 *
	 * @return static
	 */
	public function relateMany($mid, $as = null)
	{
		$this->builder->relateMany($mid, $as);
		return $this;
	}

	/**
	 * 必须先调用from方法！
	 *
	 * 表结构如下:
	 *  menu 		 --- menu_content_id
	 *  menu_content --- id, menu_type_id
	 *  menu_type	 --- id
	 *
	 * 使用示例:
	 *
	Q('menu')
	->leftJoin('menu_content AS u', 'u.id', 'menu_content_id')
	->leftJoin('menu_type AS w', 'u.menu_type_id', 'w.id')
	相当于
	Q('menu')
	->belongTo('menu_content', 'u')
	->belongTo('menu_type', 'w', 'u')

	SELECT * FROM `menu`
	LEFT JOIN `menu_content` AS `u` ON u.id = `menu`.`menu_content_id`
	LEFT JOIN `menu_type` AS `w` ON w.id = `u`.menu_type_id
	 *
	 * @return static
	 */
	public function belongsTo($table, $as = null, $table2 = null)
	{
		$this->builder->belongsTo($table, $as, $table2);
		return $this;
	}

	/**
	 * 跟上面belongsTo刚好相反, 必须先调用from方法！
	 * 表结构如下:
	 *
	 * menu_content --- id
	 * menu			--- menu_content_id

	Q('menu_content')
	->hasOne('menu')
	->readRow();

	SELECT *  FROM `menu_content`
	LEFT JOIN `menu` ON `menu`.menu_content_id = menu_content.`id` LIMIT 1
	 *
	 * @return static
	 */
	public function hasOne($table, $as = null, $table2 = null)
	{
		$this->builder->hasOne($table, $as, $table2);
		return $this;
	}

	/**
	 * 获取统计数量
	 *
	 * @return int
	 */
	public function count()
	{
		return $this->builder->count();
	}

	/**
	 *
	 * @return static
	 */
	public function sum($field, $as = 'SUM')
	{
		$this->builder->sum($field, $as);
		return $this;
	}


	/**
	 * 选择模块（表名）, from表名不支持AS
	 *
	 * @return static
	 */
	public function from($p1)
	{
		$this->builder->from($p1);
		return $this;
	}

	//******************************数据库操作方法**************************
	/**
	 * 设置sql where and字句
	 * 用法示例:
	 *  $this->where();
	 *
	 * 注意：当查询方式为"IN"时要用"[in]", "OR"要用"[or]", "LIKE"用"%*%, %*, *%"
	 * 当$p1是数组时*********************
	 * 传入: [
	'filedA' => 1,
	'filedB*%' => 3,
	'filedC%*' => 3,
	'filedD%*%' => 4,
	'filed_f>' => 5,

	'1[or]' => [
	'sun_a>=' => 'a',
	'sun_b<'  => '3'
	],
	];
	 * 	将转化为如下sql语句：
	`filed_a`="1" AND `filed_b` LIKE "2%" AND `filed_c` LIKE "%3" AND `filed_d` LIKE "%4%"
	AND `filed_f`>"5" AND ( `sun_a`>="a" OR `sun_b`<"3" )

	 ******************************
	 * 当p1 p2是字符串，p3为空时
	 * p1 = field, p2 = 1,  将转为  field="1"
	 *********************************
	 * 当p1 p2 p3均为字符串且不为空时
	 * p1是字段名, p2是查询方式, p3则是查询的字段值
	 * 注意：
	LIKE模糊查询扔用 *%, %*%的方式，如：where( 'field', '%*', '3)  ===>  'field LIKE "%3"'
	 *      IN查询，如：where( 'field', 'IN', [1, 2])   ===> 'field IN ("1", "2")' 或 where( 'field', '[in]', [1, 2])也行
	 *      其他的查询方式和mysql原生的一样
	 *
	 * @return static
	 */
	public function where($p1, $p2 = '=', $p3 = null, $table = null)
	{
		$this->builder->where($p1, $p2, $p3, $table);
		return $this;
	}

	/**
	 *
	 * @return static
	 */
	public function orWhere($p1, $p2 = '=', $p3 = null, $table = null)
	{
		$this->builder->orWhere($p1, $p2, $p3, $table);
		return $this;
	}

	/**
	 *
	 * @return static
	 */
	public function having($p1, $p2 = '=', $p3 = null, $table = null)
	{
		$this->builder->having($p1, $p2, $p3, $table);
		return $this;
	}

	/**
	 *
	 * @return static
	 */
	public function orHaving($p1, $p2 = '=', $p3 = null, $table = null)
	{
		$this->builder->orHaving($p1, $p2, $p3, $table);
		return $this;
	}
	/**
	 *  传入：
	 * [
	'id', 'parentId', 'name',
	'MenuContent' => ['content'], 'WechatMenuType' => ['code', 'menuType']
	]
	 * 返回:
	`table`.`id` AS id,`table`.`parent_id` AS parentId,`table`.`name` AS name,
	`menu_content`.`content` AS content,`wechat_menu_type`.`code` AS code,
	`wechat_menu_type`.`menu_type` AS menuTyp
	 *
	 * @return static
	 */
	public function select($data = '*')
	{
		$this->builder->select($data);

		return $this;
	}

	/**
	 *
	 * @return static
	 */
	public function field($data = '*')
	{
		$this->builder->select($data);

		return $this;
	}

	/**
	 * 用法:
	 *  $this->limit(0, 5); ===> LIMIT 0, 5
	 *
	 *  $this->limit(5);    ===> LIMIT 5
	 *
	 * @return static
	 */
	public function limit($p1, $p2 = 0)
	{
		$this->builder->limit($p1, $p2);
		return $this;
	}

	/**
	 * 用法:
	 * 	$this->update([
	 * 		'name' => '张三', 'age' => 18
	 * 	]);
	 *
	 *  $this->update('age', '+', 18);
	 *
	 *  $this->update('age', '+');
	 *
	 *  $this->update('age', '-');
	 *
	 * @return static
	 */
	public function update($data, $p2 = null, $p3 = 1)
	{
		return $this->builder->update($data, $p2, $p3);
	}

	//字段值--
	public function incr($field, $step = 1)
	{
		return $this->builder->update($field, '+', $step);
	}

	//字段值++
	public function decr($field, $step = 1)
	{
		return $this->builder->update($field, '-', $step);
	}

	/**
	 * 读取单行数据
	 *
	 * @return array
	 */
	public function readRow()
	{
		return $this->builder->readRow();
	}

	/**
	 *
	 * @return array
	 */
	public function read()
	{
		return $this->builder->read();
	}

	/**
	 *
	 * @return array
	 */
	public function find()
	{
		return $this->builder->read();
	}

	/**
	 * 获取数据库查询语句字符串
	 *
	 * @return string
	 */
	public function querySql($clear = false)
	{
		return $this->builder->querySql($clear);
	}

	/**
	 *
	 * @return array
	 */
	public function findOne()
	{
		return $this->builder->readRow();
	}

	public function one()
	{
		return $this->builder->readRow();
	}

	public function all()
	{
		return $this->builder->read();
	}

	/**
	 *
	 * @return static
	 */
	public function sort($orderString)
	{
		$this->builder->sort($orderString);
		return $this;
	}

	/**
	 * 获取where条件字符串
	 *
	 * @return string
	 */
	public function getWhereString($isHaving = false)
	{
		return $this->builder->getWhereSql($isHaving);
	}

	/**
	 * 获取绑定参数
	 *
	 * @return array
	 */
	public function getBindParams()
	{
		return $this->builder->getBindParams();
	}

	/**
	 *
	 * @return static
	 */
	public function group($data)
	{
		$this->builder->group($data);
		return $this;
	}
	/**
	 * 传入：
	 * $this->leftJoin('menu_content AS u', 'u.id', 'menu_content_id')
	->leftJoin('wechat_menu_type AS w', 'u.wechat_menu_type_id', 'w.id')
	 *
	 * 返回：
	LEFT JOIN `menu_content` AS u    ON `table`.`menu_content_id`      = `u`.`id`
	LEFT JOIN `wechat_menu_type` AS w ON `u`.`wechat_menu_type_id` = `w`.`id`
	 *
	 * @return static
	 */
	public function leftJoin($data, $p1 = null, $p2 = null)
	{
		$this->builder->leftJoin($data, $p1, $p2);
		return $this;
	}

	/**
	 *
	 * @return int
	 */
	public function insert(array $data)
	{
		return $this->builder->insert($data);
	}

	public function replace(array $data)
	{
		return $this->builder->replace($data);
	}

	public function add(array $data)
	{
		return $this->builder->insert($data);
	}

	public function batchInsert(array & $data)
	{
		return $this->builder->batchInsert($data);
	}

	public function remove($id = null)
	{
		return $this->builder->remove($id);
	}

	public function delete($id = null)
	{
		return $this->builder->remove($id);
	}

	public function reset()
	{
		$this->builder->clear();
		return $this;
	}

	public function __call($name, $arguments)
	{
		call_user_func_array([$this->builder, $name], $arguments);
		return $this;
	}

}
