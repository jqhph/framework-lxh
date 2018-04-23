<?php

use Lxh\Migration\Migrator;

use Lxh\Migration\Database\TableHelper;

class InitMigration extends Migrator
{
    /**
     * 表名配置
     *
     * @var array
     */
    protected $tables = [
        'admin'               => 'admin',
        'admin_trash'         => 'admin_trash',
        'user'                => 'user',
        'menu'                => 'menu',
        'role'                => 'roles',
        'ability'             => 'abilities',
        'assigned_abilities'  => 'assigned_abilities',
        'assigned_roles'      => 'assigned_roles',
        'admin_login_log'     => 'admin_login_log',
        'admin_operation_log' => 'admin_operation_log',
        'options'             => 'options',
    ];

    public function change()
    {
        $this->createAdmin();
        $this->createMenu();
        $this->createRoles();
        $this->createAbilities();
        $this->createAssignedAbilities();
        $this->createAssignedRoles();
        $this->createAdminLoginLog();
        $this->createAdminOperationLog();
        $this->createOptions();
    }

    protected function createAdmin()
    {
        $table = function (TableHelper $table) {
            $table->string('username')->limit(13);
            $table->string('password')->limit(200);
            $table->string('email')->limit(50);
            $table->string('mobile')->limit(30);
            $table->string('first_name')->limit(10);
            $table->string('last_name')->limit(20);
            $table->string('avatar')->limit(120);
            $table->integer('sex')->tiny()->unsigned()->comment('0未知，1男，2女');
            $table->integer('created_at')->unsigned()->default(0);
            $table->integer('updated_at')->unsigned()->default(0);
            $table->integer('last_login_ip')->unsigned()->default(0);
            $table->integer('reg_ip')->unsigned()->default(0);
            $table->boolean('is_admin')->unsigned()->default(0);
            $table->boolean('status')->unsigned()->default(1)->comment('1激活，0禁用');
            $table->integer('created_by_id')->unsigned()->default(0);
            $table->addIndex('username')->unique();
            $table->addIndex('email');
            $table->addIndex('mobile');

            $table->unsigned()
                ->innodb()
                ->utf8mb4()
                ->comment('后台用户')
                ->create();
        };

        $this->tableHelper($this->tables['admin'], $table);
        $this->tableHelper($this->tables['admin_trash'], $table);
    }

    protected function createUser()
    {
        $this->tableHelper($this->tables['user'], function (TableHelper $table) {
            $table->biginteger('id')->unsigned()->autoincrement();
            $table->string('username')->length(20);
            $table->string('password')->length(200);
            $table->string('email')->length(50);
            $table->string('mobile')->length(30);
            $table->string('first_name')->length(25);
            $table->string('last_name')->length(50);
            $table->string('avatar')->length(200);
            $table->boolean('sex')->unsigned()->default(0)->comment('0未知，1男，2女');
            $table->integer('created_at')->unsigned();
            $table->integer('updated_at')->unsigned();
            $table->integer('last_login_ip')->unsigned();
            $table->integer('last_login_time')->unsigned();
            $table->integer('reg_ip')->unsigned();
            $table->boolean('status')->unsigned()->default(1)->comment('1激活，0禁用');
            $table->addIndex('username')->unique();
            $table->addIndex('email');
            $table->addIndex('mobile');

            $table->primaryKey('id')
                ->innodb()
                ->utf8mb4()
                ->comment('前台用户')
                ->create();
        });
    }

    protected function createMenu()
    {
        $this->tableHelper($this->tables['menu'], function (TableHelper $table) {
            $table->string('name')->length(20);
            $table->string('icon')->length(100);
            $table->string('route')->length(100)->comment('路由');
            $table->boolean('use_route_prefix')->unsigned()->default(1)->comment('是否使用路由前缀');
            $table->boolean('show')->unsigned()->default(1)->comment('1显示，0不显示');
            $table->integer('parent_id')->unsigned()->default(0);
            $table->integer('created_at')->unsigned()->default(0);
            $table->integer('created_by_id')->unsigned()->default(0);
            $table->integer('priority')->tiny()->unsigned()->default(0)->comment('排序权重值，值越小排序越靠前');
            $table->integer('ability_id')->unsigned()->default(0);

            $table->unsigned()
                ->innodb()
                ->utf8mb4()
                ->comment('菜单')
                ->create();
        });
    }

    protected function createRoles()
    {
        $this->tableHelper($this->tables['role'], function (TableHelper $table) {
            $table->string('slug')->length(30)->comment('角色唯一标识，只能填英文');
            $table->string('title')->length(30)->comment('角色名称');
            $table->string('comment')->comment('描述信息');
            $table->integer('created_at')->unsigned();
            $table->integer('created_by_id')->unsigned();
            $table->integer('updated_at')->unsigned();
            $table->addIndex('slug')->unique();

            $table->unsigned()
                ->innodb()
                ->utf8mb4()
                ->comment('角色')
                ->create();
        });
    }

    protected function createAbilities()
    {
        $this->tableHelper($this->tables['ability'], function (TableHelper $table) {
            $table->string('slug')->length(30)->comment('权限唯一标识，只能填英文');
            $table->string('title')->length(30)->comment('权限名称');
            $table->boolean('forbidden')->unsigned()->default(0)->comment('允许的权限，1禁止的权限');
            $table->string('comment')->comment('描述信息');
            $table->integer('created_at')->unsigned();
            $table->integer('created_by_id')->unsigned();
            $table->integer('updated_at')->unsigned();
            $table->addIndex('slug')->unique();

            $table->unsigned()
                ->innodb()
                ->utf8mb4()
                ->comment('权限')
                ->create();
        });
    }

    protected function createAssignedAbilities()
    {
        $this->tableHelper($this->tables['assigned_abilities'], function (TableHelper $table) {
            $table->integer('ability_id')->unsigned();
            $table->integer('entity_id')->unsigned();
            $table->integer('entity_type')->unsigned()->default(2)->comment('1用户，2角色');

            $table->id(false) // 禁止自动添加主键
                ->innodb()
                ->utf8mb4()
                ->comment('权限关联角色中间表')
                ->create();
        });
    }

    protected function createAssignedRoles()
    {
        $this->tableHelper($this->tables['assigned_roles'], function (TableHelper $table) {
            $table->integer('role_id')->unsigned();
            $table->integer('entity_id')->unsigned();
            $table->integer('entity_type')->unsigned()->default(1)->comment('1用户');
            $table->addIndex(['role_id', 'entity_id', 'entity_type'])->name('role_id')->unique();

            $table->id(false)
                ->innodb()
                ->utf8mb4()
                ->comment('权限关联角色中间表')
                ->create();
        });
    }

    protected function createAdminLoginLog()
    {
        $this->tableHelper($this->tables['admin_login_log'], function (TableHelper $table) {
            $table->string('token')->length(200)->comment('登陆生成的token');
            $table->string('key')->length(200)->comment('加密盐值');
            $table->integer('created_at')->unsigned();
            $table->integer('logout_at')->unsigned()->comment('登出时间，只有当用户手动登出时才会有值');
            $table->integer('ip')->unsigned();
            $table->integer('device')->tiny()->unsigned()->comment('登录设备类型（预留）');
            $table->boolean('active')->unsigned()->default(1)->comment('1有效，0无效');
            $table->integer('life')->unsigned()->comment('登录有效期，单位秒，0表示不限制');
            $table->integer('user_id')->unsigned()->comment('登录用户id');
            $table->integer('app')->tiny()->unsigned()->comment('登录入口应用类型');
            $table->integer('type')->tiny()->unsigned()->comment('1站内session登录，2授权开放登录');
            $table->addIndex('token')->unique();

            $table->unsigned()
                ->innodb()
                ->utf8mb4()
                ->comment('后台用户登录日志')
                ->create();
        });
    }

    protected function createAdminOperationLog()
    {
        $this->tableHelper($this->tables['admin_operation_log'], function (TableHelper $table) {
            $table->integer('admin_id')->unsigned();
            $table->string('path')->length(200);
            $table->boolean('method')->unsigned()->default(1)->comment('1 GET, 2POST, 3PUT, 4DELETE, 5OPTION, 6HEAD');
            $table->integer('ip')->unsigned()->default(0);
            $table->text('input');
            $table->integer('created_at')->unsigned();
            $table->string('table')->length(50);
            $table->integer('type')->tiny()->unsigned()->default(0)->comment('0其他，1新增，2删除，3删除');
            $table->addIndex('admin_id');
            $table->addIndex('table');
            $table->addIndex('created_at');

            $table->unsigned()
                ->innodb()
                ->utf8mb4()
                ->comment('后台系统操作日志')
                ->create();
        });
    }

    protected function createOptions()
    {
        $this->tableHelper($this->tables['options'], function (TableHelper $table) {
            $table->string('name')->length(100);
            $table->string('value');
            $table->boolean('autoload')->unsigned()->default(0)->comment('是否自动加载');
            $table->addIndex('name')->unique();

            $table->unsigned()
                ->innodb()
                ->utf8mb4()
                ->comment('系统配置选项')
                ->create();
        });
    }

}
