<?php

namespace Lxh\Admin\ApiTest;
use Lxh\Admin\Admin;
use Lxh\Admin\Widgets\Tab;
use Lxh\Application;

/**
 * API测试器
 *
 * Class Manager
 * @package Lxh\Admin\ApiTest
 */
class Manager
{
    /**
     * @var Admin
     */
    protected $admin;

    public function __construct(Admin $admin)
    {
        // 增加视图命名空间
        add_view_namespace('api-test', __DIR__.'/views');

        $this->admin = $admin;

    }

    /**
     * 渲染视图
     *
     * @return string
     */
    public function render()
    {
        $tab    = new Tab();
        $parser = new ApiParser();

        $tab->add('API请求模拟', view('api-test::api-test')->render());
        $tab->add('API解析', $parser->render());

        return $this->admin
            ->content()
            ->header('API测试')
            ->description('便捷的测试工具')
            ->body($tab)
            ->render();
    }

}
