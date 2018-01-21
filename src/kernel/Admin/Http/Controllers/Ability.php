<?php

namespace Lxh\Admin\Http\Controllers;

use Lxh\Admin\Admin;
use Lxh\Auth\AuthManager;
use Lxh\Http\Request;
use Lxh\Http\Response;
use Lxh\Admin\Filter;
use Lxh\Admin\Layout\Content;
use Lxh\Admin\Widgets\Box;
use Lxh\Admin\Widgets\Form;
use Lxh\Admin\Grid;
use Lxh\Admin\Table\Table;

class Ability extends Controller
{
    protected function initialize()
    {
        Admin::model(\Lxh\Auth\Database\Ability::class);
    }

    /**
     * 是否使用过滤器
     *
     * @var bool
     */
    protected $filter = 'modal';

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
        $table->text('id')->hide()->sortable();
        $table->text('title');
        $table->text('name');
        $table->checked('forbidden');
        $table->text('comment');
        $table->date('created_at')->sortable();
        $table->date('modified_at')->sortable();
    }

    /**
     * 过滤器初始化方法
     *
     * @param Filter $filter
     */
    protected function filter(Filter $filter)
    {
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
        return trans('Please enter a unique identifier.');
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
