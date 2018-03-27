<?php

namespace Lxh\Admin\Http\Controllers;

use Lxh\Admin\Admin;
use Lxh\Admin\Data\Items;
use Lxh\Admin\Fields\Code;
use Lxh\Admin\Fields\Label;
use Lxh\Admin\Fields\Link;
use Lxh\Admin\Table\Td;
use Lxh\Admin\Table\Th;
use Lxh\Admin\Table\Tr;
use Lxh\Auth\AuthManager;
use Lxh\Http\Request;
use Lxh\Http\Response;
use Lxh\Admin\Filter;
use Lxh\Admin\Layout\Content;
use Lxh\Admin\Widgets\Box;
use Lxh\Admin\Widgets\Form;
use Lxh\Admin\Grid;
use Lxh\Admin\Table\Table;
use Lxh\Admin\Http\Models\Logs as LogsModel;
use Lxh\Support\Collection;

/**
 * 系统操作日志模块
 *
 */
class Logs extends Controller
{
    /**
     * @var string
     */
    protected $filter = true;

    protected function initialize()
    {
        // 指定模型名称
        Admin::model(LogsModel::class);
    }

    /**
     * @param Grid $grid
     * @param Content $content
     */
    protected function grid(Grid $grid)
    {
        $grid->disableBatchDelete();
        $grid->disableDelete();
        $grid->disableEdit();
        $grid->disableCreate();
        $grid->disableResponsive();
    }

    /**
     * @param Filter $filter
     */
    protected function filter(Filter $filter)
    {
//        $admins = (new Collection((new \Lxh\Auth\Database\Admin())->find()))->pluck(['']);
        
        $filter->select('admin_id')->options();
        $filter->text('table')->minlen(3)->like();
        $filter->text('input')->minlen(5)->like();
    }

    /**
     * @param Table $table
     */
    protected function table(Table $table)
    {
        $table->code('id')->sortable();

        $url = Admin::url('Admin')->detail('{value}');
        $table->link('admin_name', function (Link $link) use ($url) {
            $link->format($url, 'admin_id');
        });

        $methods = [
            1 => ['GET', 'primary'],
            2 => ['POST', 'success'],
            3 => ['PUT', 'purple'],
            4 => ['DELETE', 'danger'],
            5 => ['OPTION', 'info'],
        ];
        $table->column('method')->display(function ($value) use ($methods) {
            $label = new Label('method', $methods[$value][0]);

            $label->color($methods[$value][1]);

            return $label->render();
        });
        $table->code('path', function (Code $code) {
            $code->primary();
        });

        $table->label('ip');
        $table->label('table', function (Label $label) {
            $label->color('purple');
        });
        $table->code('input')->th(function (Th $th) {
            $th->style('width:50%;');
        });

        $types = [
            0 => ['其他', 'info'],
            1 => ['新增', 'success'],
            2 => ['修改', 'purple'],
            3 => ['删除', 'danger'],
        ];
        $table->label('type', function (Label $label) use ($types) {
            $value = $label->value();

            $selected = get_value($types, $value, ['其他', 'info']);

            $label->label($selected[0]);
            $label->color($selected[1]);
        });
        $table->date('created_at');
    }

}
