<?php

namespace Lxh\Admin\Http\Controllers;

use Lxh\Admin\Admin as AdminCreator;
use Lxh\Admin\Fields\Link;
use Lxh\Admin\Fields\Tag;
use Lxh\Admin\Filter;
use Lxh\Admin\Form\Field\MultipleSelect;
use Lxh\Admin\Layout\Content;
use Lxh\Admin\Table\Td;
use Lxh\Admin\Table\Th;
use Lxh\Admin\Table\Tr;
use Lxh\Admin\Widgets\Box;
use Lxh\Admin\Widgets\Form;
use Lxh\Admin\Grid;
use Lxh\Admin\Table\Table;
use Lxh\Auth\AuthManager;
use Lxh\Auth\Database\Models;
use Lxh\Exceptions\Forbidden;
use Lxh\Helper\Valitron\Validator;

class Admin extends Controller
{
    /**
     * @var string
     */
    protected $filter = 'modal';

    protected function initialize()
    {
    }

    /**
     * @param Grid $grid
     * @param Content $content
     */
    public function grid(Grid $grid, Content $content)
    {
    }

    protected function createModalId()
    {
    }

    public function table(Table $table)
    {
        $table->text('id')->sortable();
        $table->text('username');
        $table->text('email');
        $table->text('mobile');

        $keyName = Models::getUserKeyName();
        $table->link('roles')
            ->then(function (Link $link) use ($keyName) {
                $id = $link->row($keyName);

                $link->useAjaxModal()
                    ->title(trans('Roles'))
                    ->dataId($id)
                    ->url('/api/admin/roles-list/' .$id)
                    ->label(trans('list'));
            });

        $table->checkbox('status');
        $table->checkbox('is_admin')->hide();
        $table->select('sex');
        $table->date('created_at')->sortable();
        $table->date('modified_at')->sortable()->hide();
        $table->text('last_login_ip')->hide();
        $table->date('last_login_time')->hide();

        $table->column(3, 'name', function (array $row, Td $td, Th $th, Tr $tr) {
            return $row['first_name'] . $row['last_name'];
        });
    }

    public function filter(Filter $filter)
    {
        $filter->text('username')->like();
        $filter->text('email')->like()->right();
        $filter->text('mobile')->like()->right();
        $filter->text('name')->where(function () {
            if (! $value = I('name')) {
                // 返回null，则不会执行此字段条件查询
                return null;
            }
            $like = ['LIKE', "$value%"];

            return [
                'OR' => ['first_name' => &$like, 'last_name' => &$like]
            ];
        })
            ->formatField(false); // 使用自定义字段名称查询
    }

    protected function form(Form $form, Content $content)
    {
        $form->text('username')->rules('required|length_between[4-15]');

        if ($this->id) {
            // 修改管理员信息时，密码可以为空
            $rules = 'length_between[5-15]';
        } else {
            // 创建新的管理员账号时，必须输入密码
            $rules = 'required|length_between[5-15]';
        }
        $form->text('password')->rules($rules)->value(false);
        $form->text('email')->rules('valid_email');
        $form->text('mobile');
        $form->select('status')->options([1, 0]);

        if (auth()->isAdministrator()) {
            $form->select('is_admin')->options([0, 1]);
        }
        $form->select('sex')->options([0, 1, 2]);

        $form->multipleSelect('roles')
            ->options($this->formatRoles())
            ->help($this->getRolesHelp())
            ->attaching(function (MultipleSelect $select) {
                if ($select->row('is_admin')) {
                    $select->disabled();
                }
            });
    }

    protected function getRolesHelp()
    {
        $url = AdminCreator::url('Role')->action('Create');
        $tabid = str_replace('/', '-', $url);
        $tablabel = trans('Create Ability');

        $help = trans('Create Role');

        return "<a onclick=\"open_tab('$tabid','$url','$tablabel')\">$help</a>";
    }

    /**
     * 新增、修改接口验证规则定义
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'username' => 'required|lengthBetween:4,15',
            'password' => 'lengthBetween:5,15',
            'email' => 'email'
        ];

        if (! $this->id) {
            // 新增接口，密码是必须的
            $rules['password'] = 'required|lengthBetween:5,15';
        }

        return $rules;
    }

    protected function formatRoles()
    {
        $options = [];
        foreach ($this->model('Role')->find() as &$row) {
            $options[] = [
                'value' => $row['id'],
                'label' => $row['title']
            ];
        }
        return $options;
    }

    /**
     * 根据用户id获取用户角色列表
     *
     * @return array
     */
    public function actionRolesList(array $params)
    {
        if (! $id = get_value($params, 'id')) {
            return $this->error();
        }

        $admin = Models::user()->setId($id);
        $roleUrl = AdminCreator::url('Role');
        $abUrl = AdminCreator::url('Ability');
        $roleKey = Models::getRoleKeyName();
        $abKey = Models::getAbilityKeyName();

        $abilities = AuthManager::resolve($admin)
            ->getAbilitiesGroupByRoles()
            ->map(function ($roles, $roleTitle) use ($roleUrl, $abUrl, $roleKey, $abKey) {
                // 角色
                $tag = new Tag();
                $tag->value($roleTitle)
                    ->icon('fa fa-tags')
                    ->url($roleUrl->detail($roles[$roleKey]))
                    ->middle();

                $table = new \Lxh\Admin\Widgets\Table([$tag->render()]);

                // 权限
                $tags = '';
                foreach ($roles['abilities'] as &$ability) {
                    $tags .= (new Tag())
                        ->label($ability['title'])
                        ->url($abUrl->detail($ability[$abKey]))
                        ->render();
                }

                $table->setRows([[$tags]]);

                return $table->render();
            })->all();

        return $this->success([
            'content' => implode('<br>', $abilities),
        ]);
    }

    /**
     * 用户登录api
     *
     * @return string
     */
    public function actionLogin()
    {
        if (empty($_POST)) {
            return $this->error();
        }
        $v = $this->validator();

        $v->fill($_POST);

        $v->rule('username', 'lengthBetween', 4, 20);
        $v->rule('password', 'lengthBetween', 4, 30);

        if (! $v->validate()) {
            return $this->error($v->errors());
        }

        if (! $this->model()->login($_POST['username'], $_POST['password'], I('remember'))) {
            return $this->failed();
        }

        return $this->success();
    }

    /**
     * 注册接口
     *
     * @return array
     */
    public function actionRegister()
    {
        if (empty($_POST)) {
            return $this->error();
        }
        $v = $this->validator();

        $v->fill($_POST);

        $v->rule('username', 'lengthBetween', 4, 20);
        $v->rule('password', 'lengthBetween', 4, 30);
        $v->rule('password', 'equals', 'repassword');

        if (! $v->validate()) {
            return $this->error($v->errors());
        }

        $admin = $this->model();

        if ($admin->userExists($_POST['username'])) {
            return $this->error('The username exists.');
        }

        if (! $admin->register($_POST, $this->request->ip())) {
            return $this->failed();
        }

        $admin->login($_POST['username'], $_POST['password'], true, true);

        return $this->success();
    }
}