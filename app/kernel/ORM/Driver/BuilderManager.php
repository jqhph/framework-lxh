<?php
namespace Lxh\ORM\Driver;

class BuilderManager extends \Lxh\Basis\Factory
{
	protected $defaultName = 'Mysql';
	/**
	 * 创建一个映射器
	 * */
	public function create($name) 
	{
		$class = 'Lxh\\ORM\\Driver\\' . $name . '\\Builder';

		return new $class($this->container);
	}
	
}
