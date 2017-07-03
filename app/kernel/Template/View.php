<?php
/**
 * 模板管理
 *
 * @author Jqh
 * @date   2017/6/15 14:53
 */

namespace Lxh\Template;

use Lxh\Helper\Entity;

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

    public function __construct()
    {
        $this->vars = new Entity();
        $this->version = config('view-version', 'v1.0');
        $this->module = make('controller.manager')->moduleName();
    }

    /**
     * 分配变量到模板输出
     *
     * @param  string $key  在模板使用的变量名称
     * @param  mixed $value 变量值，此处使用引用传值，分配时变量必须先定义
     * @return void
     */
    public function assign($key, & $value = null)
    {
        $this->vars->$key = $value;
    }

    /**
     * 读取模板内容并返回
     *
     * @param  string $viewName 模板名称
     * @param  array  $vars     要传递到模板的值
     * @return string
     */
    public function fetch($viewName, array $vars = [])
    {
        // 页面缓存
        ob_start();

//        foreach ($this->vars as $k => & $v) {
//            ${$k} = & $v;
//        }

        foreach ($vars as $k => & $v) {
            $this->vars->$k = $v;
        }

        $args = $this->vars;

        // 读取模板
        include $this->getTemplatePath($viewName);

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
        return "{$this->root}application/" . $this->module . "/View/{$this->version}/$viewName.php";
    }

    /**
     * 输出模板内容
     *
     * @param  string $viewName 模板名称
     * @param  array  $vars     要传递到模板的值
     * @return void
     */
    public function display($viewName, array $vars = [])
    {
        echo $this->fetch($viewName, $vars);
    }
}
