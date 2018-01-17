<?php
/**
 * 路由接口
 *
 * @author Jqh
 * @date   2017/6/13 20:24
 */

namespace Lxh\Contracts;

interface Router
{
    const SUCCESS  = 1;
    const NOTFOUND = 404;

    /**
     * 添加路由配置
     *
     * @param  array $config
     * @return $this
     */
    public function add(array $config);

    /**
     * 设置路由配置
     *
     * @param  array $config
     * @return $this
     */
    public function fill(array $config);

    /**
     * 执行匹配操作
     *
     * @return void
     */
    public function handle();

    /**
     * 获取路由匹配结果
     *
     * @param
     * @return mixed
     */
    public function getDispatchResult();
}
