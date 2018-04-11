<?php
/**
 * Created by PhpStorm.
 * User: Jqh
 * Date: 2017/8/1
 * Time: 20:57
 */

namespace Lxh\Admin\Http\Controllers;

use Lxh\Admin\Admin;
use Lxh\Admin\Fields\Link;
use Lxh\Admin\Fields\Tag;
use Lxh\Admin\Filter;
use Lxh\Admin\Grid;
use Lxh\Admin\Layout\Content;
use Lxh\Admin\Table\Table;
use Lxh\Admin\Widgets\Form;
use Lxh\Auth\Database\Models;
use Lxh\Exceptions\Forbidden;
use Lxh\Helper\Valitron\Validator;

class Role extends Controller
{
    /**
     * @var string
     */
    protected $filter = true;

    protected $admins = [];

    protected function initialize()
    {
        Admin::model(\Lxh\Auth\Database\Role::class);
    }

    protected function table(Table $table)
    {
        $table->text('id')->hide()->sortable();
        $table->text('title');
        $table->code('slug');
        $table->text('comment');

        $this->buildAbilities($table);

        $table->date('created_at')->sortable();
        $table->date('modified_at')->sortable();

        $admins = $this->findAdminsNameKeyById();
        $table->link('created_by', function (Link $link) use ($admins) {
            $link->format(
                Admin::url('Admin')->detail('{value}'), 'created_by_id'
            );

            $link->value(
                get_value($admins, $link->item('created_by_id'))
            );
        });
    }
    
    protected function findAdminsNameKeyById()
    {
        if ($this->admins) {
            return $this->admins;
        }
        
        return $this->admins = (new \Lxh\Auth\Database\Admin())->findNameKeyById();
    }

    protected function filter(Filter $filter)
    {
        $filter->text('slug')->like();
        $filter->text('title')->like();
        $filter->dateRange('created_at')->between()->time();
        $filter->select('entity_id')
            ->options(array_flip($this->findAdminsNameKeyById()->all()))
            ->equal()
            ->formatField('ar.entity_id');
    }

    protected function form(Form $form)
    {
        if ($this->id) {
            $form->text('id')->disabled();
        }
        $form->text('title')->rules('required|length_between[2-30]');
        $form->text('slug')->rules('required|length_between[2-20]');
        $form->text('comment');
        $this->buildAbilitiesInput($form);

    }

    protected function buildAbilities(Table $table)
    {
        // 检查是否有读取权限信息的权限
        if (! auth()->can('ability.read')) {
            return;
        }
        $keyName = Models::getRoleKeyName();
        $label = trans('abilities', 'fields');
        $table->link('abilities', function (Link $link) use ($keyName, $label) {
            $id = $link->item($keyName);

            $link->useAjaxModal()
                ->title($label)
                ->url(Admin::url()->api('abilities', $id))
                ->label(trans('list'));
        });
    }

    protected function buildAbilitiesInput(Form $form)
    {
        if ($ablities = $this->formatAbilities()) {
            $url = Admin::url('Ability')->action('Create');
            $tabid = str_replace('/', '-', $url);
            $tablabel = trans('Create Ability');

            $form->tableCheckbox('abilities')
                ->rows($ablities)
                ->help(trans('Assign abilities') . " <a onclick=\"open_tab('$tabid', '$url', '$tablabel')\">[$tablabel]</a>");
        }
    }

    protected function formatAbilities()
    {
        $abilities = [];
        foreach (Models::ability()->find() as $row) {
            $abilities[] = [
                'value' => $row['id'],
                'label' => $row['title']
            ];
        }
        return $abilities;
    }

    protected function updateFilter($id, array &$input)
    {
    }

    protected function addFilter(array &$input)
    {
        if ($this->model()->select('id')->where('slug', $input['slug'])->findOne()) {
            return $input['slug'] . ' already exist.';
        }
    }

    /**
     * 权限列表查看接口
     *
     * @param array $params
     * @return array
     */
    public function actionAbilities(array $params)
    {
        if (! auth()->can('ability.read')) {
            throw new Forbidden();
        }

        if (! $id = get_value($params, 'id')) {
            return $this->error();
        }

        $url = Admin::url('Ability');

        $role = Models::role()->setId($id);

        $tags = $role->findAbilitiesForRole()->map(function ($ability) use ($url) {
            $tag = new Tag();
            $tag->label($ability['title'] ?: $ability['slug']);
            $tag->url($url->detail($ability['ability_id']));
            return $tag->render();
        });

        return '<div style="padding:10px 15px">' . implode('', $tags->all()) . '</div>';
    }

    // 字段验证规则
    protected function rules()
    {
//        return [
//            'slug' => 'required|between:2,30',
//            'title' => 'required|between:2,30',
//        ];
    }

}
