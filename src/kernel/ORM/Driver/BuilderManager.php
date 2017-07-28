<?php
namespace Lxh\ORM\Driver;

use Lxh\ORM\Query;

class BuilderManager extends \Lxh\Basis\Factory
{
	protected $defaultName = 'Mysql';

	/**
	 * @var Query
	 */
	protected $query;

	/**
	 * 创建一个映射器
	 * */
	public function create($name) 
	{
		$class = 'Lxh\\ORM\\Driver\\' . $name . '\\Builder';

		return new $class($this->container, $this->query);
	}

	public function setQuery(Query $query)
	{
		$this->query = $query;
	}
	
}
