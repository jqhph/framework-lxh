<?php

namespace Lxh\MVC;

use Lxh\Basis\Factory;

/**
 * model工厂类
 */
class ModelFactory extends Factory
{
	public function create($name)
	{
		$className = 'Lxh\\' . __MODULE__ . '\\Models\\' . $name;

		if (! class_exists($className)) {
			if ($default = config('default-model')) {
				$className = $default;
			} else {
				$className = 'Lxh\\MVC\\Model';
			}
		}

		return new $className($name, $this->container);
	}
	
}
