<?php

namespace Lxh\RequestAuth\Entities;

use Lxh\Contracts\Support\Arrayable;

class Log implements Arrayable
{
    public $id;
    public $token;
    // 加密随机字符串
    public $key;
    // 登录时间
    public $created_at;
    // 登出时间
    public $logout_at = 0;
    // 登录ip
    public $ip;
    // 登录设备（预留字段）
    public $device;
    // 是否有效 1 0
    public $active;
    // token有效期（秒）
    public $life;
    // 登录用户id
    public $user_id;
    // 应用类型，必须是一个0-99的整数
    // 对于站内session模式登录，此参数用于保证用户在同一类型下的应用只能保留一个有效的登陆状态
    // 对于开放授权token模式登录，此参数用于保证用户在同一类型下的应用只能获取一个有效授权token
    public $app;
    // 1站内session登录，2授权开放登录
    public $type;


    public function __construct(array $items = [])
    {
        $this->attach($items);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'id'         => &$this->id,
            'token'      => &$this->token,
            'key'        => &$this->key,
            'created_at' => &$this->created_at,
            'logout_at'  => &$this->logout_at,
            'ip'         => &$this->ip,
            'device'     => &$this->device,
            'active'     => &$this->active,
            'life'       => &$this->life,
            'user_id'    => &$this->user_id,
            'app'        => &$this->app,
            'type'       => &$this->type,
        ];
    }

    /**
     * @param array $items
     * @return $this
     */
    public function attach(array $items)
    {
        foreach ($items as $k => &$v) {
//            if (isset($this->$k)) {
            $this->$k = $v;
//            }
        }
        return $this;
    }
}
