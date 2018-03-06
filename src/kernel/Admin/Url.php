<?php

namespace Lxh\Admin;

use Lxh\Helper\Util;

class Url
{
    /**
     *
     * @var array
     */
    protected static $instances = [];

    /**
     * @var string
     */
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
    public function index()
    {
        return '/' . $this->prefix;
    }

    /**
     * 前台主页
     *
     * @return string
     */
    public function home()
    {
        return '/';
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
     * 文件上传路径
     *
     * @return string
     */
    public function upload()
    {
        return "/{$this->prefix}/{$this->scope}/action/upload";
    }

    /**
     * 图片上传路径
     *
     * @return string
     */
    public function uploadImage()
    {
        return "/{$this->prefix}/{$this->scope}/action/upload-image";
    }

    public function deleteFile()
    {
        return "/{$this->prefix}/{$this->scope}/action/delete-file";
    }

    /**
     * 图片查看路径
     *
     * @param $filename
     * @return string
     */
    public function image($filename)
    {
        return "/image/$filename";
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

    public function profile()
    {
        return "/{$this->prefix}/admin/action/profile";
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
     * @return string
     */
    public function detail($id)
    {
        return "/{$this->prefix}/$this->scope/view/$id";
    }

    /**
     * @param $id
     * @return string
     */
    public function updateField($id)
    {
        return "/{$this->prefix}/api/$this->scope/update-field/$id";
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
