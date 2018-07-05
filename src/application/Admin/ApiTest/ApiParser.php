<?php

namespace Lxh\Admin\ApiTest;

use Lxh\Contracts\Support\Renderable;

/**
 * API测试器
 *
 * Class Manager
 * @package Lxh\Admin\ApiTest
 */
class ApiParser implements Renderable
{
    /**
     * 视图名称
     *
     * @var string
     */
    protected $view = 'api-test::api-parser';

    /**
     * 渲染视图
     *
     * @return string
     */
    public function render()
    {
        return view($this->view)->render();
    }

}
