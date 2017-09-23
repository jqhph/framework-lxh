<?php
/**
 * 模板管理
 *
 * @author Jqh
 * @date   2017/6/15 14:53
 */

namespace Lxh\Template;

use Lxh\Exceptions\Error;
use Lxh\Exceptions\InvalidArgumentException;
use Lxh\Helper\Entity;
use Lxh\MVC\ControllerManager;

class View
{
    /**
     * 模板变量管理对象
     *
     * @var Entity
     */
    protected $vars = [];

    protected $module;

    // 模板版本
    protected $version;

    protected $root = __ROOT__;

    protected $dir = '';

    public function __construct(ControllerManager $manager)
    {
        $this->version = config('view-version', 'v1.0');
        $this->module = $manager->moduleName();

        $this->dir = "{$this->root}application/{$this->module}/View/{$this->version}/";
    }

    /**
     * 分配变量到模板输出
     * 通过此方法分配的变量所有引入的模板都可用
     *
     * @param  string $key  在模板使用的变量名称
     * @param  mixed $value 变量值，此处使用引用传值，分配时变量必须先定义
     * @return void
     */
    public function with($key, & $value = null)
    {
        if (is_array($key)) {
            $this->vars = array_merge($this->vars, $key);
        } else {
            $this->vars[$key] = $value;
        }
    }

    /**
     * 读取模板内容并返回
     *
     * @param  string $viewName 模板名称
     * @param  array  $vars     要传递到模板的值，只有当前模板可以用
     * @return string
     */
    public function render($viewName, array & $vars = [])
    {
        // 页面缓存
        ob_start();

        foreach ($this->vars as $k => & $v) {
            ${$k} = & $v;
        }

        foreach ($vars as $k => & $v) {
            ${$k} = & $v;
        }

        // 读取模板
        $path = $this->getTemplatePath($viewName);
        if (! is_file($path)) {
            throw new InvalidArgumentException("View [$path] not found.");
        }

        include $path;

        // 获取并清空缓存
        return ob_get_clean();
    }

    /**
     * 获取模板路径
     *
     * @param  string $viewName 模板名称
     * @return string
     */
    public function getTemplatePath($viewName)
    {
        $viewName = str_replace('.', '/', $viewName);

        return "{$this->dir}/$viewName.php";
    }

    /**
     * 判断模板是否存在
     *
     * @param  string $name 路径名称
     * @return bool
     */
    public function exist($name)
    {
        return is_file($this->getTemplatePath($name)) ? true : false;
    }

}
