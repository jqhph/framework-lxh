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
    protected function scope($scope)
    {
        $this->scope = lc_dash($scope);

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
     * 登录url
     *
     * @return string
     */
    public function login()
    {
        return "/{$this->prefix}/login";
    }

    /**
     * 注册url
     *
     * @return string
     */
    public function register()
    {
        return "/{$this->prefix}/register";
    }

    /**
     * 登出url
     *
     * @return string
     */
    public function logout()
    {
        return "/{$this->prefix}/logout";
    }

    /**
     * 普通自定义action
     *
     * @param $action
     * @param $id
     * @return string
     */
    public function action($action = __ACTION__, $id = null)
    {
        $action = lc_dash($action);

        $id = $id ? "/$id" : '';

        return "/{$this->prefix}/$this->scope/action/{$action}{$id}";
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
    public function update($id)
    {
        return "/{$this->prefix}/$this->scope/update/$id";
    }

    /**
     * @param $id
     * @return string
     */
    public function api($action, $id = null)
    {
        $id = $id ? "/$id" : '';
        return "/{$this->prefix}/api/$this->scope/{$action}{$id}";
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
