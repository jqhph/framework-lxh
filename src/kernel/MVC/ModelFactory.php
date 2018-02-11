<?php

namespace Lxh\MVC;

use Lxh\Basis\Factory;

/**
 * model工厂类
 */
class ModelFactory extends Factory
{
	/**
	 * @param $name
	 * @return Model
	 */
	public function create($name)
	{
		if (! $name) {
			$name = $this->getDefaultName();
		}

		if (strpos($name, "\\")) {
			$className = $name;
			$name = explode('\\', $name);
			$name = end($name);
		} else {
			$className = 'Lxh\\' . __MODULE__ . '\\Models\\' . $name;

			if (! class_exists($className)) {
				if ($default = config('default-model')) {
					$className = $default;
				} else {
					$className = 'Lxh\\MVC\\Model';
				}
			}
		}

		return new $className($name, $this->container);
	}

	/**
	 * @return string
	 */
	public function getDefaultName()
	{
		if (! $this->defaultName) {
			$this->defaultName = __CONTROLLER__;
		}

		return $this->defaultName; // TODO: Change the autogenerated stub
	}

}
