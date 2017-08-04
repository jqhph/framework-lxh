<?php
/**
 * 服务注册配置文件
 *
 * @author Jqh
 * @date   2017/6/13 20:14
 */

$config = [];

$config['acl-menu'] = [
    'shared' => true,
    'class' => 'Lxh\Admin\Acl\Menu',
];

$config['front.client'] = [
    'shared' => true,
    'class' => 'Lxh\Kernel\Client'
];

$config['page'] = [
    'shared' => true,
    'class' => 'Lxh\Kernel\Support\Page'
];

$config['code.generator'] = [
    'shared' => true,
    'class' => 'Lxh\Kernel\Builder\CodeGenerator'
];

return $config;
