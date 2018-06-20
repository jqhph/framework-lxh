<?php

namespace Lxh\Database\Events;

use Lxh\Application;
use Lxh\Debug\Records\Database as Record;

class Database
{
    /**
     * 监听数据库连接事件方法
     *
     * @param string $command 数据库连接信息
     * @param array $data 预处理绑定参数
     * @param double|float $usetime 使用时间
     */
    public function connect($command, array $data, $usetime)
    {
        if (is_prod()) {
            return;
        }

        Application::$container->tracer->addDatabaseRecord(
            new Record($command, $data, $usetime)
        );
    }

    /**
     * 监听数据库命令执行事件
     *
     * @param string $command 数据库命令
     * @param array $data 预处理绑定参数
     * @param double|float $usetime 使用时间
     */
    public function query($command, array $data, $usetime)
    {
        if (is_prod()) {
            return;
        }

        Application::$container->tracer->addDatabaseRecord(
            new Record($command, $data, $usetime)
        );
    }

    /**
     * 监听数据库异常事件
     *
     * @param \PDOException $e
     * @param string $command
     * @param array $data
     */
    public function exception(\PDOException $e, $command, array $data)
    {
        Application::$container->tracer->addDatabaseRecord(
            new Record($command, $data, 0.00, $e)
        );
    }
}
