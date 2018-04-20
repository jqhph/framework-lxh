<?php

/**
 * 命令行配置
 *
 * @author Jqh
 * @date   2018/4/20 14:36
 */
return [
    'paths' => [
        'application/Command',
    ],

    'namespaces' => [
        'Lxh\\Command\\',
    ],

    'commands' => [
        'migrate:init'       => Lxh\Migration\Console\Command\Init::class,
        'migrate:create'     => Lxh\Migration\Console\Command\Create::class,
        'migrate'            => Lxh\Migration\Console\Command\Migrate::class,
        'migrate:rollback'   => Lxh\Migration\Console\Command\Rollback::class,
        'migrate:status'     => Lxh\Migration\Console\Command\Status::class,
        'migrate:breakpoint' => Lxh\Migration\Console\Command\Breakpoint::class,
        'migrate:test'       => Lxh\Migration\Console\Command\Test::class,
        'seed:create'        => Lxh\Migration\Console\Command\SeedCreate::class,
        'seed:run'           => Lxh\Migration\Console\Command\SeedRun::class,
    ],
];
