<?php

use Phinx\Seed\AbstractSeed;

class DefaultAdmin extends AbstractSeed
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
    }

    protected function attachAdmin()
    {
        $this->insert($this->tables['admin'], [
            [
                'id' => 1,
                'username' => 'admin',
                'password' => Lxh\Support\Password::encrypt('admin'),
                'first_name' => 'test',
                'last_name' => 'admin',
                'created_at' => time(),
                'status' => 1,
                'is_admin' => 1,
            ],
        ]);
    }
}
