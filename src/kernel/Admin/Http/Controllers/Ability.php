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
        parent::initialize();
    }

    /**
     * 是否使用过滤器
     *
     * @var bool
     */
    protected $filter = true;

    /**
     * Grid初始化方法
     *
     * @param Grid $grid
     */
    protected function grid(Grid $grid)
    {
    }

    /**
     * Table初始化方法
     *
     * @param Table $table
     */
    protected function table(Table $table)
    {
        $table->code('id')->sortable()->desc();
        $table->text('title');
        $table->code('slug');
        $table->checked('forbidden');
        $table->text('comment');
        $table->date('created_at')->sortable();
        $table->date('updated_at')->sortable();
    }

    /**
     * 过滤器初始化方法
     *
     * @param Filter $filter
     */
    protected function filter(Filter $filter)
    {
        $filter->text('slug')->like();
        $filter->text('title')->like();
        $filter->dateRange('created_at')->between()->time();
    }

    /**
     * Form初始化方法
     *
     * @param Form $form
     */
    protected function form(Form $form)
    {
        $support = \Lxh\Auth\Ability::getAbilitiesSupport();

        if ($this->id) {
            $form->text('id')->disabled();
        }

        $form->text('title')->rules('required|length_between[2-30]');
        $form->text('slug')->options($support)->help($this->getNameHelp())->rules('required|length_between[2-40]');
        $form->text('comment');
        $form->switch('forbidden');
    }

    protected function getNameHelp()
    {
        return trans('Please enter a unique identifier.');
    }

    protected function addFilter(array &$input)
    {
        if ($this->model()->select($this->model()->getKeyName())->where('slug', $input['slug'])->findOne()) {
            return $input['slug'] . ' already exist.';
        }
    }

    public function actionAll()
    {
        return $this->model()->find();
    }
}
