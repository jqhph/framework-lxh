<?php

return [
    'paths' => [
        'migrations' => [
            '%%PHINX_CONFIG_DIR%%/../resource/database/migrations',

        ],
        'seeds' => [
            '%%PHINX_CONFIG_DIR%%/../resource/database/seeds',
        ],
    ],

    'migration_base_class' => Lxh\Migration\Migrator::class,

    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_database' => ENV_DEV,
        ENV_DEV => [
            'adapter' => 'mysql',
            'host' => 'localhost',
            'name' => 'lxh',
            'user' => 'root',
            'pass' => '',
            'port' => 3306,
            'charset' => 'utf8',
        ],
    ],

    'version_order' => 'creation',
];
