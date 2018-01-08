<?php

namespace Lxh\Auth\Conductors;

use Lxh\Auth\AuthManager;
use Lxh\Auth\Helpers;
use Lxh\ORM\Query;
use Lxh\Support\Collection;
use Lxh\Auth\Database\Models;
use Lxh\MVC\Model;

class FindRoles
{
    /**
     * @var Model
     */
    protected $authority;

    /**
     * 当前用户拥有的权限
     *
     * @var array
     */
    protected $abilities = [];

    public function __construct(Model $authority)
    {
        $this->authority = $authority;
    }

    /**
     *
     * @return Collection
     */
    public function find()
    {
        $role = Models::role();

        if (! $this->abilities) {
            return new Collection($role->findByAuthority($this->authority));
        }

        // 根据id查询
        $roleIds = (new Collection($this->abilities))->pluck('role_id')->all();

        if (count($roleIds) > 1) {
            $where = [$role->getKeyName(), ['IN', &$roleIds]];
        } else {
            $where = [$role->getKeyName() => $roleIds];
        }
        
        return new Collection($role->where($where)->find());
    }
}