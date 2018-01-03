<?php

namespace Lxh\Admin;

use Lxh\Helper\Util;

class Url
{
    protected static $instances = [];

    protected $prefix = 'admin';

    /**
     *
     * @var string
     */
    protected $scope;

    public function __construct($scope)
    {
        $this->scope($scope);
    }

    /**
     * @param string $scope
     * @return static
     */
    public static function create($scope = __CONTROLLER__)
    {
        return isset(static::$instances[$scope]) ?: (static::$instances[$scope] = new static($scope));
    }

    /**
     * @param $scope
     * @return $this
     */
    public function scope($scope)
    {
        $this->scope = Util::convertWith($scope, true, '-');

        return $this;
    }

    public function prefix($prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * 主页url
     *
     * @return string
     */
    public function home()
    {
        return '/' . $this->prefix;
    }

    /**
     * 普通自定义action
     *
     * @param $action
     * @param $controller
     * @return string
     */
    public function action($action = __ACTION__)
    {
        $action = Util::convertWith($action, true, '-');

        return "/{$this->prefix}/$this->scope/$action";
    }

    /**
     * 生成读取视图详情url
     *
     * @return $this
     */
    public function detail($id)
    {
        return "/{$this->prefix}/$this->scope/view/$id";
    }

    /**
     * @param $id
     * @return string
     */
    public function api($action)
    {
        return "/{$this->prefix}/api/$this->scope/action/$action";
    }

    /**
     * 读取数据接口
     *
     * @param $id
     * @return string
     */
    public function apiView($id)
    {
        return "/{$this->prefix}/api/$this->scope/view/$id";
    }
}
