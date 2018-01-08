<?php

namespace Lxh\Auth\Database;

use Lxh\Admin\MVC\Model;
use Lxh\MVC\Model AS Base;
use Lxh\Support\Collection;

class Role extends Model
{
    use Concerns\IsRole, Concerns\FindOrCreate;

    /**
     * 权限实体类型
     *
     * @var int
     */
    protected $morphType = 2;


    protected function initialize()
    {
        $this->tableName = Models::table('roles');
    }

    public function getMorphType()
    {
        return $this->morphType;
    }

    /**
     * 创建并返回数据
     *
     * @param array $names
     * @param array $attributes
     * @return array
     */
    public function createAndReturn(array $names, array $attributes = [])
    {
        $attributes = $names + $this->formatCreateAttributes($attributes);
        $newId = $this->query()->insert($attributes);

        if (! $newId) return [];

        $attributes[$this->idFieldsName] = $newId;
        return $attributes;
    }

    protected function formatCreateAttributes(array &$attributes = [])
    {
        return array_merge([
            'created_at' => time(),
            'created_by_id' => admin()->getId()
        ], $attributes);
    }

    /**
     * 查找出用户所拥有的角色
     *
     * @return array
     */
    public function findByAuthority(Base $user)
    {
        $assignedRoles = Models::table('assigned_roles');
        $userType = $user->getMorphType();

        return $this->query()
            ->joinRaw("LEFT JOIN $assignedRoles ar ON (ar.role_id = {$this->tableName}.id AND ar.entity_type = $userType)")
            ->where('ar.entity_id', $user->getId())
            ->find();
    }

    /**
     * 重置用户已分配角色
     *
     * @param Model $user
     * @return mixed
     */
    public function resetAssigned(Base $user)
    {
        $where = [
            'entity_id' => $user->getId(),
            'entity_type' => $user->getMorphType()
        ];

        return query()->from(Models::table('assigned_roles'))->where($where)->delete();
    }
}
