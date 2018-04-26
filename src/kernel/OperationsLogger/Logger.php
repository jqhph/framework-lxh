<?php

namespace Lxh\OperationsLogger;

use Lxh\OperationsLogger\Entities\AdminAction;
use Lxh\MVC\Model;

/**
 * 操作日志
 *
 * @method AdminAction adminAction(Model $model = null)
 */
class Logger
{
    protected static $availableEntities = [
        'adminAction' => AdminAction::class,
    ];

    /**
     *
     * @var array
     */
    protected $entities = [];

    public function __construct()
    {
    }


    /**
     * 新增一条操作日志
     *
     * @return $this
     */
    public function push(Entities\Entity $entity)
    {
        $this->entities[] = $entity;

        return $this;
    }

    /**
     * 返回所有日志实体
     *
     * @return array
     */
    public function entities()
    {
        return $this->entities;
    }

    /**
     * Register custom entity.
     *
     * @param string $abstract
     * @param string $class
     *
     * @return void
     */
    public static function extend($abstract, $class)
    {
        static::$availableEntities[$abstract] = $class;
    }

    public function __call($method, $arguments)
    {
        if ($className = getvalue(static::$availableEntities, $method)) {
            $entity = new $className(getvalue($arguments, 0, null));

            $this->push($entity);

            return $entity;
        }
    }
}
