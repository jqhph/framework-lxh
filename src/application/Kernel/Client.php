<?php
/**
 * 前端管理
 *
 * @author Jqh
 * @date   2017/7/31 13:54
 */
namespace Lxh\Kernel;

class Client
{
    /**
     * 清除客户端缓存
     * 强制更新
     *
     * @return bool
     */
    public function clearCache()
    {
        return make('config')->save(['js-version' => time(), 'css-version' => time()]);
    }

    /**
     * 更新客户端缓存
     * 有使用缓存才更新
     *
     * @return bool
     */
    public function updateCache()
    {
        if (config('replica-client-config.use-cache')) {
            return make('config')->save(['js-version' => time()]);
        }
        return false;
    }
}
