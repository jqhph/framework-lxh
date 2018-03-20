<?php

namespace Lxh\Admin\Http\Controllers;

use Lxh\Admin\Admin as AdminCreator;
use Lxh\Admin\Data\Items;
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
use Lxh\Cache\Item;
use Lxh\Exceptions\Forbidden;
use Lxh\Helper\Valitron\Validator;
use Lxh\Http\Url;
use Lxh\Auth\Database\Admin as AdminModel;
use Lxh\OAuth\User;
use Lxh\Session\Store;

class Admin extends Controller
{
    /**
     * 使用过滤器
     * 
     * @var string
     */
    protected $filter = true;

    /**
     * 图片上传字段
     *
     * @var array
     */
    protected $uploads = ['avatar' => 'image', ];

    /**
     * 使用回收站
     *
     * @var bool
     */
    protected $trash = true;

    protected function initialize()
    {
        AdminCreator::model(AdminModel::class);
    }

    /**
     * @param Grid $grid
     * @param Content $content
     */
    public function grid(Grid $grid)
    {
        $grid->rowActions(function (Grid\RowActions $rowActions) {
            if ($rowActions->getId() == 1) {
                $rowActions->disableDelete();
            }
        });
    }

    public function table(Table $table)
    {
        $table->code('id')->sortable();
        $table->text('username');
        $table->column('name')->display(function ($value, Items $items) {
            return $items->column('first_name') . $items->column('last_name');
        });
        $table->email('email');
        $table->text('mobile');

        $this->buildRoles($table);

        $table->checked('status');
        $table->checked('is_admin')->hide();
        $table->select('sex');
        $table->date('created_at')->sortable();
        $table->date('modified_at')->sortable()->hide();
        $table->text('last_login_ip')->hide();
        $table->date('last_login_time')->hide();
    }

    protected function buildRoles(Table $table)
    {
        if (!auth()->can('role.read')) {
            return;
        }

        $keyName = Models::getUserKeyName();
        $table->link('roles', function (Link $link) use ($keyName) {
            $id = $link->item($keyName);
            $api = AdminCreator::url()->api('roles-list', $id);

            $link->useAjaxModal()
                ->title(trans('Roles'))
                ->dataId($id)
                ->url($api)
                ->label(trans('list'));
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

    protected function form(Form $form)
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

        $form->image('avatar');
        $form->switch('status')->checked();

        if (auth()->isAdministrator()) {
            $form->switch('is_admin');
        }
        $form->select('sex')->options([0, 1, 2]);

        $form->multipleSelect('roles')
            ->options($this->formatRoles())
            ->help($this->getRolesHelp())
            ->attaching(function (MultipleSelect $select) {
                if ($select->item('is_admin')) {
                    $select->disabled();
                }
            });
    }

    protected function getRolesHelp()
    {
        $url = AdminCreator::url('Role')->action('Create');
        $tabid = str_replace('/', '-', $url);

        $help = trans('Create Role');

        return "<a onclick=\"open_tab('$tabid','$url','$help')\">$help</a>";
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
        foreach (Models::role()->find() as &$row) {
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
        $auth = auth();
        if (!$auth->can('role.read')) {
            throw new Forbidden();
        }

        if (! $id = get_value($params, 'id')) {
            return $this->error();
        }

        $selected = Models::user()->setId($id);
        $selected = AuthManager::resolve($selected);
        $roleUrl = AdminCreator::url('Role');
        $roleKey = Models::getRoleKeyName();

        if (! $auth->can('ability.read')) {
            // 如果没有权限查看权限，则只显示角色信息
            $tags = '';
            foreach ($selected->roles()->all() as &$row) {
                $tags .= (new Tag())
                    ->label($row['title'])
                    ->url($roleUrl->detail($row[$roleKey]))
                    ->render();
            }

            return $this->success([
                'content' => &$tags,
            ]);
        }

        // 查看角色和角色所拥有的权限
        $abUrl = AdminCreator::url('Ability');
        $abKey = Models::getAbilityKeyName();

        $abilities = $selected
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

        $rules = [
            'username' => 'required|lengthBetween:4,20',
            'password' => 'required|lengthBetween:4,30',
        ];

        $session = session();

        if ($captchas = $session->get('_captcha')) {
            $rules['captcha'] = 'required|length:5';
        }

        $v = $this->validator($_POST, $rules);

        if (! $v->validate()) {
            return $this->error($v->errors());
        }

        // 验证验证码
        if ($captchas) {
            if ($msg = $this->validateCaptcha($captchas))  {
                return $msg;
            }
        }

        $oauth = admin()->oauth();
        
        if (! $oauth->login($_POST['username'], $_POST['password'], I('remember'))) {
            if ($oauth->failTimes() > config('admin.show-captcha-times', 5)) {
                // 保存session，当页面刷新时显示验证码
                $session->save('is_required_captcha', 1);

                return $this->message('Failed', 10047);
            }
            
            return $this->failed();
        }
        $this->clearCaptcha($oauth, $session);

        $target = Url::referer() ?: AdminCreator::url()->index();

        return $this->success(['target' => $target]);
    }

    protected function validateCaptcha(array $captchas)
    {
        $code = $captchas['code'];
        $time = $captchas['at'];
        if ($time + config('admin.captcha-life', 120) < time()) {
            return $this->message(trans('The authenticator code has expired.'), 10049);
        }

        if (strtolower($code) != strtolower($_POST['captcha'])) {
            return $this->error(trans('The authenticator code is incorrect.'));
        }
    }

    protected function clearCaptcha(User $oauth, Store $session)
    {
        // 清除失败记录
        $oauth->resetFailTimes();
        // 清除验证码相关session
        $session->delete('_captcha');
        $session->delete('is_required_captcha');
    }

    /**
     * 登出
     *
     */
    public function actionLogout()
    {
        admin()->oauth()->logout();

        $this->response->redirect(
            AdminCreator::url()->login()
        );
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
        $v = $this->validator($_POST, [
            'username' => 'required|lengthBetween:4,20',
            'password' => 'required|lengthBetween:4,30|equals:repassword',
        ]);

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