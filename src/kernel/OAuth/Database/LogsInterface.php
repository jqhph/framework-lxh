<?php

namespace Lxh\OAuth\Database;

interface LogsInterface
{
    /**
     * 获取登陆日志数据
     *
     * @param mixed $id
     * @param mixed $token
     * @return array
     */
    public function find($id = null, $token = null);

    /**
     * 查找token加密随机码
     *
     * @param $id
     * @param $token
     * @return array|mixed
     */
    public function findEncryptCode($id, $token);

    /**
     *
     * @param mixed $key
     * @return array|mixed
     */
    public function item($key = null);

    /**
     * 登出操作
     *
     * @return mixed
     */
    public function logout();

    /**
     * 把token状态设置为无效
     *
     * @param $userId
     * @param $logId
     * @param $token
     * @return mixed
     */
    public function inactive($userId, $logId, $token);

    /**
     * 生成日志记录
     *
     * @param User $user
     * @param bool $remember
     */
    public function create(User $user, $remember = false);
}
