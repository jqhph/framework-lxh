<?php

namespace Lxh\Database\Events;

class Database
{
    /**
     * 监听数据库连接事件方法
     *
     * @param string $command 数据库连接信息
     * @param array $data 预处理绑定参数
     * @param double|float $usetime 使用时间
     */
    public function connect($command, $data, $usetime)
    {
        resolve('track')
            ->record('db', [
                'command' => &$command,
                'type' => 'c',
                'usetime' => &$usetime,
                'params' => &$data
            ]);
    }

    /**
     * 监听数据库命令执行事件
     *
     * @param string $command 数据库命令
     * @param array $data 预处理绑定参数
     * @param double|float $usetime 使用时间
     */
    public function query($command, $data, $usetime)
    {
        resolve('track')
            ->record('db', [
                'command' => &$command,
                'type' => 'unknown',
                'usetime' => &$usetime,
                'params' => &$data
            ]);
    }
}
