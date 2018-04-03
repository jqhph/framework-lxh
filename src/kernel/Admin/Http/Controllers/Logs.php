<?php

namespace Lxh\Admin\Http\Controllers;

use Lxh\Admin\Admin;
use Lxh\Admin\Data\Items;
use Lxh\Admin\Fields\Code;
use Lxh\Admin\Fields\Expand;
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
use Lxh\OperationsLogger\Entities\AdminAction;
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

    protected $admins = [];

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
        $filter->text('input')->width(4)->minlen(5)->like();
        $filter->text('table');
        $filter->text('path');
        $filter->text('ip')->where(function () {
            $ip = I('ip');

            return $ip ? ip2long($ip) : false;
        });
        $filter->select('type')->options(
            array_flip(AdminAction::types())
        );
        $filter->select('admin_id')->options(array_flip($this->findAdminsNameKeyById()->all()));
    }

    /**
     * 获取管理员数据
     *
     * @return array|Collection
     */
    protected function findAdminsNameKeyById()
    {
        if ($this->admins) {
            return $this->admins;
        }

        return $this->admins = (new \Lxh\Auth\Database\Admin())->findNameKeyById();
    }

    /**
     * @param Table $table
     */
    protected function table(Table $table)
    {
        $colors = [
            1 => 'primary',
            2 => 'success',
            3 => 'danger',
            4 => 'purple',
            5 => 'info',
            6 => 'inverse',
            7 => 'default',
            8 => 'success',
            9 => 'pink',
            0 => 'primary',
        ];

        $methods = [
            1 => ['GET', 'primary'],
            2 => ['POST', 'success'],
            3 => ['PUT', 'purple'],
            4 => ['DELETE', 'danger'],
            5 => ['OPTION', 'info'],
        ];

//        $table->code('id')->sortable()->hide();

        $table->code('table');

        $label = trans('input', 'fields');
        $table->expand('input', function (Expand $expand) use ($label) {
            $expand->label($label);
            $expand->content(
                '<div style="padding:20px"><code>'.$expand->value().'</code></div>'
            );
        });

        $table->column('method')->display(function ($value) use ($methods) {
            $label = new Label('method', $methods[$value][0]);

            $label->color($methods[$value][1]);

            return $label->render();
        });
        $table->code('path', function (Code $code) {
            $code->primary();
        });

        $table->ip('ip');

        $types = AdminAction::types();
        $table->label('type', function (Label $label) use ($types, $colors) {
            $value = $label->value();

            $selected = get_value($types, $value, 'other');

            $label->label(trans($selected));

            if ($value > 9) {
                $value = $value % 9;
            }

            $label->color($colors[$value]);
        });

        $admins = $this->findAdminsNameKeyById();
        $url = Admin::url('Admin')->detail('{value}');
        $table->link('admin_name', function (Link $link) use ($url, $admins) {
            $link->format($url, 'admin_id');

            $link->value(
                get_value($admins, $link->item('admin_id'))
            );
        });

        $table->date('created_at');
    }

}
