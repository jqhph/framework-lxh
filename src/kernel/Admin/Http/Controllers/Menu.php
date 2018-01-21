<?php
/**
 * Created by PhpStorm.
 * User: Jqh
 * Date: 2017/7/16
 * Time: 12:57
 */

namespace Lxh\Admin\Http\Controllers;

use Lxh\Admin\Admin;
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
     * 网格字段定义
     *
     * @var array
     */
    protected $grid = [
        'id' => ['hide' => 1, 'sortable' => 1, 'desc' => 1],
        'icon' => ['view' => 'Icon'],
        'name',
        'controller',
        'action',
        'show' => ['view' => 'Checked'],
        'type' => ['view' => 'Select'],
        'priority',
    ];

    protected function initialize()
    {
        Admin::model(\Lxh\Auth\Database\Menu::class);
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

    // 删除操作验证方法
    public function deleteable($id)
    {
        // 判断是否是系统菜单，如果是则不允许删除
        if ($this->model()->isSystem($id)) {
            return trans('Can\'t delete the system menu!');
        }
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
    protected function form(Form $form, Content $content)
    {
        $form->selectTree('parent_id')->options(auth()->menu()->all())->defaultOption(0, trans('Top'));
        $form->text('name')->rules('required');
        $form->text('icon')->help($this->iconHelp());
        $form->text('controller');
        $form->text('action');
        $form->select('show')->options([1, 0])->default(1);
        $form->select('priority')->options(range(0, 30))->help(trans('The smaller the value, the higher the order.'));
        $this->buildQuickCreateAbilityInput($form);
        $this->buildAbilitiesInput($form);
    }

    /**
     * 创建快捷创建并关联权限表单
     *
     * @param Form $form
     */
    protected function buildQuickCreateAbilityInput(Form $form)
    {
        $form->text('quick_relate_ability')
            ->options(Ability::getAbilitiesSupport())
            ->help(trans('Please fill the ability name.the ablity will be create if no exists.'));
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
    protected function grid(Grid $grid, Content $content)
    {
        $grid->rows(auth()->menu()->all());
        $grid->disablePagination();
    }

    /**
     * 表格定义
     *
     * @param Table $table
     */
    protected function table(Table $table)
    {
        $table->useTree('subs');

        $table->link('ability_title')->rendering(function (Link $link) {
            $link->format(
                Admin::url('Ability')->detail('{value}'), 'ability_id'
            );
        });
    }


}
