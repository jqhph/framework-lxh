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

	/**
	 * @var string
	 */
	protected $connectionName = 'primary';

	/**
	 * @var string
	 */
	protected $defaultConnectionName = 'primary';

	public function __construct(Container $container)
	{
		$this->builder = new Builder($container, $this);
		$this->container = $container;
	}

	/**
	 * @param string $name
	 * @return $this
	 */
	public function setConnectionName($name)
	{
		$this->connectionName = $name;
		return $this;
	}

	/**
	 * 设置或获取数据库连接实例
	 *
	 * @param null $name
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

		return $this->connection = pdo($this->defaultConnectionName);
	}

	/**
	 * 多对多关联(不支持AS别名)
	 *
	 * @param $mid string 中间表表名
	 * @param $relate string 要关联的表
	 * @return $this
	 */
	public function manyToMany($mid, $relate)
	{
		$this->builder->manyToMany($mid, $relate);
		return $this;
	}

	/**
	 * INSERT IGNORE INTO `tb` SET ...
	 *
	 * @return $this
	 */
	public function ignore()
	{
		$this->builder->ignore();
		return $this;
	}

	/**
	 * @param $mid
	 * @param null $as
	 * @return $this
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
		->from('menu')
		->leftJoin('menu_content AS u', 'u.id', 'menu_content_id')
		->leftJoin('menu_type AS w', 'u.menu_type_id', 'w.id')
		相当于
		->from('menu')
		->belongTo('menu_content', 'u')
		->belongTo('menu_type', 'w', 'u')

		SELECT * FROM `menu`
		LEFT JOIN `menu_content` AS `u` ON u.id = `menu`.`menu_content_id`
		LEFT JOIN `menu_type` AS `w` ON w.id = `u`.menu_type_id
	 *
	 * @param $table
	 * @param null $as
	 * @param null $table2
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
	 * @param $table
	 * @param null $as
	 * @param null $table2
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

	/**
	 * 设置sql where and字句
	 *
	 * @return $this
	 */
	public function where($p1, $p2 = '=', $p3 = null, $table = null)
	{
		$this->builder->where($p1, $p2, $p3, $table);
		return $this;
	}

	/**
	 * @param $whereString
	 * @param array $prepareData
	 */
	public function whereRaw($whereString, array $prepareData = [])
	{
		$this->builder->whereRaw($whereString, $prepareData);

		return $this;
	}

	/**
	 * @param $whereString
	 * @param array $prepareData
	 */
	public function havingRaw($whereString, array $prepareData = [])
	{
		$this->builder->havingRaw($whereString, $prepareData);

		return $this;
	}

	/**
	 *
		->whereOr([
			'test' => 1, 'test2' => 2,
		])
	 *
	 * @param array $where 必须传入一维数组
	 * @return $this
	 */
	public function whereOr(array $where)
	{
		$type = 'OR';
		$this->builder->where($type, $where);
		return $this;
	}

	/**
	 * @param array $where
	 * @return $this
	 */
	public function havingOr(array $where)
	{
		$type = 'OR';
		$this->builder->having($type, $where);
		return $this;
	}

	/**
	 * Or字句由多个条件合并而成
	 *
		->whereOrs([
			['test' => 1, 'test2' => 2],
			['field1' => 1, 'field2' => 2],
		])

		===>
		AND ((`table`.`test` = ? AND `table`.`test2` = ?) OR (`table`.`field1` = ? AND `table`.`field2` = ?))
	 *
	 * @param array $where 必须传入二维数组
	 * @return $this
	 */
	public function whereOrs(array $where)
	{
		$where = [
			'OR+' => $where
		];
		$this->builder->where($where);
		return $this;
	}

	/**
	 * @param array $where
	 * @return $this
	 */
	public function havingOrs(array $where)
	{
		$where = [
			'OR+' => $where
		];
		$this->builder->having($where);
		return $this;
	}

	/**
	 * 使用OR连接子句
	 *
	 * @return static
	 */
	public function orWhere($p1, $p2 = '=', $p3 = null, $table = null)
	{
		$this->builder->orWhere($p1, $p2, $p3, $table);
		return $this;
	}

	/**
	 * @param $field
	 * @return $this
	 */
	public function whereNull($field)
	{
		$this->builder->where($field, 'IS NULL');
		return $this;
	}

	/**
	 * @param $field
	 * @return $this
	 */
	public function whereNotNull($field)
	{
		$this->builder->where($field, 'IS NOT NULL');
		return $this;
	}

	/**
	 * @param $field
	 * @param $p1
	 * @param $p2
	 * @return $this
	 */
	public function whereBetween($field, $p1, $p2)
	{
		$this->builder->where($field, 'BETWEEN', [&$p1, &$p2]);
		return $this;
	}

	/**
	 * @param $field
	 * @param $p1
	 * @return $this
	 */
	public function whereIn($field, array $p1)
	{
		$this->builder->where($field, 'IN', $p1);

		return $this;
	}

	/**
	 * LIKE "%value%"
	 *
	 * @param $field
	 * @param $p1
	 * @return $this
	 */
	public function whereLike($field, $p1)
	{
		$this->builder->where($field, '%*%', $p1);
		return $this;
	}

	/**
	 * LIKE "value%"
	 *
	 * @param $field
	 * @param $p1
	 * @return $this
	 */
	public function whereLikeRight($field, $p1)
	{
		$this->builder->where($field, '*%', $p1);
		return $this;
	}

	/**
	 * LIKE "value%"
	 *
	 * @param $field
	 * @param $p1
	 * @return $this
	 */
	public function whereLikeLeft($field, $p1)
	{
		$this->builder->where($field, '%*', $p1);
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
	 *
	 * @param array|string $data
	 * @return $this
	 */
	public function select($data = '*')
	{
		$this->builder->select($data);

		return $this;
	}

	/**
	 * @param array|string $data
	 * @return $this
	 */
	public function field($data = '*')
	{
		$this->builder->select($data);

		return $this;
	}

	/**
	 * @param $p1
	 * @param int $p2
	 * @return $this
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
	 *  $this->update('age', '+');
	 *  $this->update('age', '-');
	 *
	 * @param $data
	 * @param null $p2
	 * @param int $p3
	 * @return $this
	 */
	public function update($data, $p2 = null, $p3 = 1)
	{
		return $this->builder->update($data, $p2, $p3);
	}

	/**
	 * 字段值--
	 *
	 * @param $field
	 * @param int $step
	 * @return bool
	 */
	public function incr($field, $step = 1)
	{
		return $this->builder->update($field, '+', $step);
	}

	/**
	 * 字段值++
	 *
	 * @param $field
	 * @param int $step
	 * @return bool
	 */
	public function decr($field, $step = 1)
	{
		return $this->builder->update($field, '-', $step);
	}

	/**
	 *
	 * @return array
	 */
	public function find()
	{
		return $this->builder->find();
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
		return $this->builder->findOne();
	}

	/**
	 * @return array
	 * @throws \Lxh\Exceptions\InternalServerError
	 */
	public function one()
	{
		return $this->builder->findOne();
	}

	/**
	 * @return array
	 * @throws \Lxh\Exceptions\InternalServerError
	 */
	public function all()
	{
		return $this->builder->find();
	}

	/**
	 * @param $orderString
	 * @return $this
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
	 * @param $data
	 * @return $this
	 */
	public function group($data)
	{
		$this->builder->group($data);
		return $this;
	}
	/**
	 *
	 * @return $this
	 */
	public function leftJoin($table, $field1 = null, $field2 = null, $condit = '=')
	{
		$this->builder->leftJoin($table, $field1, $field2, $condit);
		return $this;
	}

	/**
	 * @param $table
	 * @param null $field1
	 * @param null $field2
	 * @param string $condit
	 * @param string $type LEFT|RIGHT
	 * @return $this
	 */
	public function join($table, $field1 = null, $field2 = null , $condit = '=', $type = '')
	{
		$this->builder->join($table, $field1, $field2, $condit, $type);
		return $this;
	}

	/**
	 * @param $string
	 * @return $this
	 */
	public function joinRaw($string)
	{
		$this->builder->joinRaw($string);
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

	/**
	 * @param array $data
	 * @return mixed
	 */
	public function replace(array $data)
	{
		return $this->builder->replace($data);
	}

	/**
	 * @param array $data
	 * @return bool
	 */
	public function add(array $data)
	{
		return $this->builder->insert($data);
	}

	/**
	 * @param array $data
	 * @return mixed
	 */
	public function batchInsert(array & $data)
	{
		return $this->builder->batchInsert($data);
	}

	/**
	 * @param array $data
	 * @return mixed
	 */
	public function batchReplace(array & $data)
	{
		return $this->builder->batchReplace($data);
	}

	/**
	 * @param null $id
	 * @return mixed
	 */
	public function remove($id = null)
	{
		return $this->builder->remove($id);
	}

	/**
	 * @param null $id
	 * @return mixed
	 */
	public function delete($id = null)
	{
		return $this->builder->remove($id);
	}

}
