<?php

namespace Lxh\Auth\Database;

use Lxh\Admin\MVC\Model;

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
     * 重置用户已分配角色
     *
     * @param Model $user
     * @return mixed
     */
    public function resetAssigned(Model $user)
    {
        $where = [
            'entity_id' => $user->getId(),
            'entity_type' => $user->getMorphType()
        ];

        return query()->from(Models::table('assigned_roles'))->where($where)->delete();
    }
}
