<?php
/**
 * 初始化配置文件
 *
 * @author Jqh
 * @date   2017/6/13 18:18
 */

define('ENV_TEST', 'test'); // 测试环境
define('ENV_DEV', 'dev');   // 开发环境
define('ENV_PROD', 'prod'); // 生产环境

// 定义当前环境
define('__ENV__', ENV_DEV);

// 载入公共常量配置文件
require __DIR__ . '/define.php';
// 载入环境初始化配置文件
require __DIR__ . '/' . __ENV__ . '/ini.php';
