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
    protected $filter = 'modal';

    protected function grid(Grid $grid, Content $content)
    {
        $grid->allowBatchDelete();
    }

    protected function table(Table $table)
    {
        $table->text('id')->hide()->sortable();
        $table->text('title');
        $table->text('name');
        $table->text('comment');

        $keyName = Models::getRoleKeyName();
        $label = trans('Abilities');
        $table->link('abilities')
            ->then(function (Link $link) use ($keyName, $label) {
                $id = $link->row($keyName);

                $link->useAjaxModal()
                    ->title($label)
                    ->dataId($id)
                    ->url('/api/role/abilities/' .$id)
                    ->label(trans('list'));
            });
        $table->date('created_at')->sortable();
        $table->date('modified_at')->sortable();
        $table->link('created_by')->then(function (Link $link) {
            $link->format(
                Admin::url('Admin')->detail('{value}'), 'created_by_id'
            );
        });
    }

    protected function filter(Filter $filter)
    {
        $filter->useModal();
        $filter->text('name')->like();
        $filter->text('title')->like();
        $filter->dateRange('created_at')->between()->toTimestamp();
    }

    protected function form(Form $form, Content $content)
    {
        $form->text('title')->rules('required|length_between[2-30]');
        $form->text('name')->rules('required|length_between[2-20]');
        $form->text('comment');
        $this->buildAbilitiesInput($form);

    }

    protected function buildAbilitiesInput(Form $form)
    {
        if ($ablities = $this->formatAbilities()) {
            $url = Admin::url('Ability')->action('Create');
            $tabid = str_replace('/', '-', $url);
            $tablabel = trans('Create Ability');

            $form->tableCheckbox('abilities')
                ->rows($ablities)
                ->color('danger')
                ->help(trans('Assign abilities') . " <a onclick=\"open_tab('$tabid', '$url', '$tablabel')\">[$tablabel]</a>");
        }
    }

    protected function formatAbilities()
    {
        $abilities = [];
        foreach ($this->model('Ability')->find() as $row) {
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
        if ($this->model()->select('id')->where('name', $input['name'])->findOne()) {
            return $input['name'] . ' already exist.';
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
        if (! $id = get_value($params, 'id')) {
            return $this->error();
        }

        $url = Admin::url('Ability');

        $role = Models::role()->setId($id);

        $tags = $role->findAbilitiesForRole()->map(function ($ability) use ($url) {
            return (new Tag())->label($ability['title'])
                ->url($url->detail($ability['ability_id']))
                ->render();
        });

        return $this->success([
            'content' => &$tags,
        ]);
    }

    // 字段验证规则
    protected function rules()
    {
//        return [
//            'name' => 'required|between:2,30',
//            'title' => 'required|between:2,30',
//        ];
    }

}
