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
        'admin' => 'admin',
        'admin_trash' => 'admin_trash',
        'user' => 'user',
        'menu' => 'menu',
        'role' => 'roles',
        'ability' => 'abilities',
        'assigned_abilities' => 'assigned_abilities',
        'assigned_roles' => 'assigned_roles',
        'admin_login_log' => 'admin_login_log',

        'post' => 'post',
        ''
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
            $table->integer('modified_at')->unsigned()->default(0);
            $table->integer('last_login_ip')->unsigned()->default(0);
            $table->integer('reg_ip')->unsigned()->default(0);
            $table->integer('is_admin')->tiny()->unsigned()->default(0);
            $table->boolean('status')->unsigned()->default(1)->comment('1激活，0禁用');
            $table->integer('created_by_id')->unsigned()->default(0);

            $table->unsigned()->innodb()->utf8mb4()->comment('后台用户表')->create();
        };

        $this->tableHelper($this->tables['admin'], $table);
        $this->tableHelper($this->tables['admin_trash'], $table);
    }

    protected function createUser()
    {
        $this->tableHelper($this->tables['user'], function (TableHelper $table) {

            $table->unsigned()->innodb()->utf8mb4()->comment('用户表')->create();
        });
    }

    protected function createMenu()
    {
        $this->tableHelper($this->tables['menu'], function (TableHelper $table) {
            $table->string('name')->length(20);
            $table->string('icon')->length(100);
            $table->integer('show')->tiny()->default(0)->comment('1显示，0不显示');
            $table->integer('parent_id')->default(0);
            $table->integer('layer')->tiny()->default(1)->comment('菜单层级');
            $table->integer('created_at')->default(0);
            $table->integer('created_by_id')->default(0);
            $table->integer('type')->tiny()->unsigned()->default(1)->comment('1普通菜单，2系统菜单');
            $table->integer('priority')->tiny()->unsigned()->default(0)->comment('排序权重值，值越小排序越靠前');
            $table->integer('ability_id')->unsigned()->default(0);
            $table->string('route')->length(100)->comment('路由');
            $table->integer('use_route_prefix')->tiny()->unsigned()->default(1)->comment('是否使用路由前缀');

            $table->unsigned()->innodb()->utf8mb4()->comment('菜单表')->create();
        });
    }

    protected function createRoles()
    {
        $this->tableHelper($this->tables['role'], function (TableHelper $table) {

            $table->unsigned()->innodb()->utf8mb4()->comment('角色表')->create();
        });
    }

    protected function createAbilities()
    {
        $this->tableHelper($this->tables['ability'], function (TableHelper $table) {

            $table->unsigned()->innodb()->utf8mb4()->comment('权限表')->create();
        });
    }

    protected function createAssignedAbilities()
    {
        $this->tableHelper($this->tables['assigned_abilities'], function (TableHelper $table) {

            $table->unsigned()->innodb()->utf8mb4()->comment('权限关联角色中间表')->create();
        });
    }

    protected function createAssignedRoles()
    {
        $this->tableHelper($this->tables['assigned_roles'], function (TableHelper $table) {

            $table->unsigned()->innodb()->utf8mb4()->comment('权限关联角色中间表')->create();
        });
    }

    protected function createAdminLoginLog()
    {
        $this->tableHelper($this->tables['admin_login_log'], function (TableHelper $table) {

            $table->unsigned()->innodb()->utf8mb4()->comment('权限关联角色中间表')->create();
        });
    }

}
