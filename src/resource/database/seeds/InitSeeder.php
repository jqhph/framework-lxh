<?php

use Phinx\Seed\AbstractSeed;
use Lxh\Support\Password;

class InitSeeder extends AbstractSeed
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
        'admin_operation_log' => 'admin_operation_log',
        'options' => 'options',
    ];

    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $this->attachAdmin();
        $this->attachMenu();
        $this->attachAbilities();
    }

    protected function attachAdmin()
    {
        $this->insert($this->tables['admin'], [
            [
                'id' => 1,
                'username' => 'admin',
                'password' => Password::encrypt('admin'),
                'first_name' => 'test',
                'last_name' => 'admin',
                'created_at' => time(),
                'status' => 1,
                'is_admin' => 1,
            ],
        ]);
    }

    protected function attachMenu()
    {
        $time = time();

        $this->insert($this->tables['menu'], [
            [
                'id' => 1,
                'name' => '系统设置',
                'icon' => 'fa fa-gears',
                'route' => '',
                'use_route_prefix' => 1,
                'show' => 1,
                'parent_id' => 0,
                'created_at' => $time,
                'priority' => 2,
                'ability_id' => 1,
            ],
            [
                'id' => 2,
                'name' => '菜单管理',
                'icon' => '',
                'route' => '/menu/action/list',
                'use_route_prefix' => 1,
                'show' => 1,
                'parent_id' => 1,
                'created_at' => $time,
                'priority' => 0,
                'ability_id' => 2,
            ],
            [
                'id' => 3,
                'name' => '语言包管理',
                'icon' => '',
                'route' => '/language/action/list',
                'use_route_prefix' => 1,
                'show' => 1,
                'parent_id' => 1,
                'created_at' => $time,
                'priority' => 3,
                'ability_id' => 7,
            ],
            [
                'id' => 4,
                'name' => '设置',
                'icon' => '',
                'route' => '/system/action/setting',
                'use_route_prefix' => 1,
                'show' => 1,
                'parent_id' => 1,
                'created_at' => $time,
                'priority' => 6,
                'ability_id' => 10,
            ],
            [
                'id' => 5,
                'name' => '系统操作日志',
                'icon' => '',
                'route' => '/logs/action/list',
                'use_route_prefix' => 1,
                'show' => 1,
                'parent_id' => 1,
                'created_at' => $time,
                'priority' => 0,
                'ability_id' => 11,
            ],
            [
                'id' => 6,
                'name' => '用户管理',
                'icon' => 'fa fa-user',
                'route' => '',
                'use_route_prefix' => 1,
                'show' => 1,
                'parent_id' => 0,
                'created_at' => $time,
                'priority' => 0,
                'ability_id' => 12,
            ],
            [
                'id' => 7,
                'name' => '管理员',
                'icon' => '',
                'route' => '/admin/action/list',
                'use_route_prefix' => 1,
                'show' => 1,
                'parent_id' => 6,
                'created_at' => $time,
                'priority' => 0,
                'ability_id' => 13,
            ],
            [
                'id' => 8,
                'name' => '角色',
                'icon' => '',
                'route' => '/role/action/list',
                'use_route_prefix' => 1,
                'show' => 1,
                'parent_id' => 6,
                'created_at' => $time,
                'priority' => 1,
                'ability_id' => 18,
            ],
            [
                'id' => 9,
                'name' => '权限',
                'icon' => '',
                'route' => '/ability/action/list',
                'use_route_prefix' => 1,
                'show' => 1,
                'parent_id' => 6,
                'created_at' => $time,
                'priority' => 2,
                'ability_id' => 23,
            ],
        ]);
    }

    protected function attachAbilities()
    {
        $time = time();

        $this->insert($this->tables['ability'], [
            [
                'id' => 1,
                'slug' => 'system.manager',
                'title' => '系统设置',
                'forbidden' => 0,
                'comment' => '系统设置菜单',
                'created_at' => $time,
                'created_by_id' => 0,
                'updated_at' => 0,
            ],
            [
                'id' => 2,
                'slug' => 'menu.read',
                'title' => '查看菜单列表',
                'forbidden' => 0,
                'comment' => '',
                'created_at' => $time,
                'created_by_id' => 0,
                'updated_at' => 0,
            ],
            [
                'id' => 3,
                'slug' => 'menu.create',
                'title' => '创建菜单',
                'forbidden' => 0,
                'comment' => '',
                'created_at' => $time,
                'created_by_id' => 0,
                'updated_at' => 0,
            ],
            [
                'id' => 4,
                'slug' => 'menu.detail',
                'title' => '查看菜单详情页',
                'forbidden' => 0,
                'comment' => '',
                'created_at' => $time,
                'created_by_id' => 0,
                'updated_at' => 0,
            ],
            [
                'id' => 5,
                'slug' => 'menu.update',
                'title' => '修改菜单',
                'forbidden' => 0,
                'comment' => '',
                'created_at' => $time,
                'created_by_id' => 0,
                'updated_at' => 0,
            ],
            [
                'id' => 6,
                'slug' => 'menu.delete',
                'title' => '删除菜单',
                'forbidden' => 0,
                'comment' => '',
                'created_at' => $time,
                'created_by_id' => 0,
                'updated_at' => 0,
            ],
            [
                'id' => 7,
                'slug' => 'language.read',
                'title' => '查看语言包列表',
                'forbidden' => 0,
                'comment' => '语言包管理菜单',
                'created_at' => $time,
                'created_by_id' => 0,
                'updated_at' => 0,
            ],
            [
                'id' => 8,
                'slug' => 'language.update',
                'title' => '修改或新增语言包',
                'forbidden' => 0,
                'comment' => '',
                'created_at' => $time,
                'created_by_id' => 0,
                'updated_at' => 0,
            ],
            [
                'id' => 9,
                'slug' => 'language.delete',
                'title' => '删除语言包',
                'forbidden' => 0,
                'comment' => '',
                'created_at' => $time,
                'created_by_id' => 0,
                'updated_at' => 0,
            ],
            [
                'id' => 10,
                'slug' => 'system.setting',
                'title' => '系统设置菜单',
                'forbidden' => 0,
                'comment' => '',
                'created_at' => $time,
                'created_by_id' => 0,
                'updated_at' => 0,
            ],
            [
                'id' => 11,
                'slug' => 'logs.read',
                'title' => '查看后台系统操作日志列表',
                'forbidden' => 0,
                'comment' => '',
                'created_at' => $time,
                'created_by_id' => 0,
                'updated_at' => 0,
            ],
            [
                'id' => 12,
                'slug' => 'user.manager',
                'title' => '用户管理菜单',
                'forbidden' => 0,
                'comment' => '',
                'created_at' => $time,
                'created_by_id' => 0,
                'updated_at' => 0,
            ],
            [
                'id' => 13,
                'slug' => 'admin.read',
                'title' => '查看管理员列表',
                'forbidden' => 0,
                'comment' => '',
                'created_at' => $time,
                'created_by_id' => 0,
                'updated_at' => 0,
            ],
            [
                'id' => 14,
                'slug' => 'admin.create',
                'title' => '创建管理员',
                'forbidden' => 0,
                'comment' => '',
                'created_at' => $time,
                'created_by_id' => 0,
                'updated_at' => 0,
            ],
            [
                'id' => 15,
                'slug' => 'admin.detail',
                'title' => '查看管理员详情页',
                'forbidden' => 0,
                'comment' => '',
                'created_at' => $time,
                'created_by_id' => 0,
                'updated_at' => 0,
            ],
            [
                'id' => 16,
                'slug' => 'admin.update',
                'title' => '修改管理员',
                'forbidden' => 0,
                'comment' => '',
                'created_at' => $time,
                'created_by_id' => 0,
                'updated_at' => 0,
            ],
            [
                'id' => 17,
                'slug' => 'admin.delete',
                'title' => '删除管理员',
                'forbidden' => 0,
                'comment' => '',
                'created_at' => $time,
                'created_by_id' => 0,
                'updated_at' => 0,
            ],
            [
                'id' => 18,
                'slug' => 'role.read',
                'title' => '查看角色列表',
                'forbidden' => 0,
                'comment' => '',
                'created_at' => $time,
                'created_by_id' => 0,
                'updated_at' => 0,
            ],
            [
                'id' => 19,
                'slug' => 'role.create',
                'title' => '创建角色',
                'forbidden' => 0,
                'comment' => '',
                'created_at' => $time,
                'created_by_id' => 0,
                'updated_at' => 0,
            ],
            [
                'id' => 20,
                'slug' => 'role.detail',
                'title' => '查看角色详情页',
                'forbidden' => 0,
                'comment' => '',
                'created_at' => $time,
                'created_by_id' => 0,
                'updated_at' => 0,
            ],
            [
                'id' => 21,
                'slug' => 'role.update',
                'title' => '修改角色',
                'forbidden' => 0,
                'comment' => '',
                'created_at' => $time,
                'created_by_id' => 0,
                'updated_at' => 0,
            ],
            [
                'id' => 22,
                'slug' => 'role.delete',
                'title' => '删除角色',
                'forbidden' => 0,
                'comment' => '',
                'created_at' => $time,
                'created_by_id' => 0,
                'updated_at' => 0,
            ],

            [
                'id' => 23,
                'slug' => 'ability.read',
                'title' => '查看权限列表',
                'forbidden' => 0,
                'comment' => '',
                'created_at' => $time,
                'created_by_id' => 0,
                'updated_at' => 0,
            ],
            [
                'id' => 24,
                'slug' => 'ability.create',
                'title' => '创建权限',
                'forbidden' => 0,
                'comment' => '',
                'created_at' => $time,
                'created_by_id' => 0,
                'updated_at' => 0,
            ],
            [
                'id' => 25,
                'slug' => 'ability.detail',
                'title' => '查看权限详情页',
                'forbidden' => 0,
                'comment' => '',
                'created_at' => $time,
                'created_by_id' => 0,
                'updated_at' => 0,
            ],
            [
                'id' => 26,
                'slug' => 'ability.update',
                'title' => '修改权限',
                'forbidden' => 0,
                'comment' => '',
                'created_at' => $time,
                'created_by_id' => 0,
                'updated_at' => 0,
            ],
            [
                'id' => 27,
                'slug' => 'ability.delete',
                'title' => '删除权限',
                'forbidden' => 0,
                'comment' => '',
                'created_at' => $time,
                'created_by_id' => 0,
                'updated_at' => 0,
            ],
        ]);
    }
}
