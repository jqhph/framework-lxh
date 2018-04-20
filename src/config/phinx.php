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

    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_database' => 'development',
        'production' => [
            'adapter' => 'mysql',
            'host' => 'localhost',
            'name' => 'production_db',
            'user' => 'root',
            'pass' => '',
            'port' => 3306,
            'charset' => 'utf8',
        ],
    ],

    'version_order' => 'creation',
];
