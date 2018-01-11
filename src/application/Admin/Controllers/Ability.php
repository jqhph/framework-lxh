<?php
/**
 *
 * @author Jqh
 * @date   2018-01-06 10:46:18
 */

namespace Lxh\Admin\Controllers;

use Lxh\Auth\AuthManager;
use Lxh\Http\Request;
use Lxh\Http\Response;
use Lxh\Admin\Filter;
use Lxh\Admin\Layout\Content;
use Lxh\Admin\Widgets\Box;
use Lxh\Admin\Widgets\Form;
use Lxh\Admin\Grid;
use Lxh\Admin\Table\Table;
use Lxh\Admin\MVC\Controller;

class Ability extends Controller
{
     /**
     * 网格字段配置
     *
     * @var array
     */
    protected $grid = [];

    /**
     * 是否使用过滤器
     *
     * @var bool
     */
    protected $filter = true;

    public function initialize()
    {
    }

    /**
     * Grid初始化方法
     *
     * @param Grid $grid
     */
    protected function grid(Grid $grid, Content $content)
    {
    }

    /**
     * Table初始化方法
     *
     * @param Table $table
     */
    protected function table(Table $table)
    {
        $table->field('id')->hide()->sortable();
        $table->field('title');
        $table->field('name');
        $table->field('forbidden')->view('Checkbox');
        $table->field('comment');
        $table->field('created_at')->view('date')->sortable();
        $table->field('modified_at')->view('date')->sortable();
    }

    /**
     * 过滤器初始化方法
     *
     * @param Filter $filter
     */
    protected function filter(Filter $filter)
    {
        $filter->useModal();
        $filter->text('name')->like();
        $filter->text('title')->like();
        $filter->dateRange('created_at')->between()->toTimestamp();
    }

    /**
     * Form初始化方法
     *
     * @param Form $form
     */
    protected function form(Form $form, Content $content)
    {
        $support = \Lxh\Auth\Ability::getAbilitiesSupport();
        
        $form->text('title')->rules('required|length_between[2-30]');
        $form->text('name')->options($support)->help($this->getNameHelp())->rules('required|length_between[2-40]');
        $form->text('comment');
        $form->select('forbidden')->options([0, 1]);
    }

    protected function getNameHelp()
    {
        return '请输入权限唯一标识。如是常用增删改查权限，请选择下拉菜单中对应的选项！';
    }

    protected function addFilter(array &$input)
    {
        if ($this->model()->select('id')->where('name', $input['name'])->findOne()) {
            return $input['name'] . ' already exist.';
        }
    }

    public function actionAll()
    {
        return $this->model()->find();
    }

}
