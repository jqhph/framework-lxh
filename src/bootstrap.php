<?php
/**
 * 引导程序
 *
 * @author Jqh
 * @date   2017/6/13 18:26
 */

require __DIR__ . '/vendor/autoload.php';

$app = new Lxh\Application();

$app->handle();

$app->response->send();
