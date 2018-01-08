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
        if (! $this->abilities) {
            return new Collection(Models::role()->findByAuthority($this->authority));
        }
        

    }
}