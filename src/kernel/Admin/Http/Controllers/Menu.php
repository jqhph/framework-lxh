<?php
/**
 * Created by PhpStorm.
 * User: Jqh
 * Date: 2017/7/16
 * Time: 12:57
 */

namespace Lxh\Admin\Http\Controllers;

use Lxh\Admin\Admin;
use Lxh\Admin\Fields\Editable;
use Lxh\Admin\Fields\Link;
use Lxh\Admin\Grid;
use Lxh\Admin\Kernel\Url;
use Lxh\Admin\Layout\Content;
use Lxh\Admin\Table\Table;
use Lxh\Admin\Widgets\Box;
use Lxh\Admin\Widgets\Form;
use Lxh\Auth\Ability;
use Lxh\Auth\AuthManager;
use Lxh\Auth\Database\Models;
use Lxh\Exceptions\Forbidden;
use Lxh\Http\Request;
use Lxh\Http\Response;
use Lxh\Helper\Valitron\Validator;
use Lxh\Admin\Layout\Row;

class Menu extends Controller
{
    /**
     * 使用权限管理
     *
     * @var bool
     */
    protected $useAuthorize = true;

    protected function initialize()
    {
        Admin::model(\Lxh\Auth\Database\Menu::class);
        $this->useAuthorize = config('use-authorize');

        parent::initialize();
    }

    /**
     * 修改前字段验证
     *
     * @param  array
     * @return mixed
     */
    protected function updateable($id, array & $fields)
    {
        if ($fields['parent_id'] == $id) {
            return trans('Can\'t put self as a parent');
        }
    }

    /**
     * 表单字段验证规则
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'name' => 'required',
            'priority' => 'required|integer',
            'icon' => 'lengthBetween:4,30',
            'controller' => 'lengthBetween:1,15',
            'action' => 'lengthBetween:1,15',
            'parent_id' => 'required'
        ];
    }

    protected function iconHelp()
    {
        $url = Admin::url('PublicEntrance')->action('font-awesome');

        $label = trans_with_global('fontawesome icon CSS');

        return "<a onclick=\"open_tab('f-a-h', '{$url}', '$label')\">$label</a>";
    }

    /**
     * 编辑界面表单定义
     *
     * @param Form $form
     */
    protected function form(Form $form)
    {
        if ($this->id) {
            $form->text('id')->disabled();
        }

        $form->selectTree('parent_id')->options(auth()->menu()->all())->defaultOption(0, trans('Top'));
        $form->text('name')->rules('required');
        $form->text('icon')->help($this->iconHelp());
        $form->text('route')->prepend('<i class="fa fa-internet-explorer"></i>');
        $form->switch('use_route_prefix')->checked()->small()->help('自动加上前缀“'.config('admin.route-prefix').'”');
        $form->switch('show')->checked()->small();
        $form->select('priority')->options(range(0, 30))->help(trans('The smaller the value, the higher the order.'));
        if ($this->useAuthorize) {
            $this->buildQuickCreateAbilityInput($form);
            $this->buildAbilitiesInput($form);
        }
    }

    /**
     * 创建快捷创建并关联权限表单
     *
     * @param Form $form
     */
    protected function buildQuickCreateAbilityInput(Form $form)
    {
//        $form->text('quick_relate_ability')
//            ->options(Ability::getAbilitiesSupport())
//            ->help(trans('Please fill the ability name.the ablity will be create if no exists.'));
    }

    protected function buildAbilitiesInput(Form $form)
    {
        if ($ablities = $this->formatAbilities()) {
            $url = Admin::url('Ability')->action('Create');
            $tabid = str_replace('/', '-', $url);
            $tablabel = trans('Create Ability');

            $form->select('ability_id')
                ->options($ablities)
                ->allowClear()
                ->defaultOption()
                ->help("<a onclick=\"open_tab('$tabid', '$url', '$tablabel')\">[$tablabel]</a>");
        }
    }

    protected function formatAbilities()
    {
        $abilities = [];

        $keyName = Models::getAbilityKeyName();

        foreach (Models::ability()->find() as &$row) {
            $abilities[] = [
                'value' => $row[$keyName],
                'label' => $row['title']
            ];
        }
        return $abilities;
    }

    /**
     * 网格定义
     *
     * @param Grid $grid
     */
    protected function grid(Grid $grid)
    {
        $grid->rows(auth()->menu()->all());
        $grid->disablePagination();
        $grid->disableBatchDelete();
    }

    /**
     * 表格定义
     *
     * @param Table $table
     */
    protected function table(Table $table)
    {
        $table->useTree('subs');

        $table->code('id');
        $table->icon('icon');
        $table->text('name');
        $table->editable('route');
        $table->switch('use_route_prefix');
        $table->switch('show');
        $table->select('type');
        $table->editable('priority', function (Editable $editable) {
            $editable->select(range(0, 30));
        });

        if ($this->useAuthorize) {
            $table->link('ability_title', function (Link $link) {
                $link->format(
                    Admin::url('Ability')->detail('{value}'), 'ability_id'
                );
            });
        }

    }


}
