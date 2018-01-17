<?php

namespace Lxh\Auth\Database;

use Lxh\Auth\AuthManager;
use Lxh\MVC\Model;
use Lxh\Support\Collection;

class Ability extends Model
{
    use Concerns\IsAbility, Concerns\FindOrCreate;

    protected $tableName = 'abilities';

    /**
     * 根据权限名称创建权限
     *
     * @param $name
     */
    protected function createAndReturn($names, array $attributes = [])
    {
        $attributes = $this->formatCreateAttributes((array)$names, $attributes);

        $this->attach($attributes);

        $newId = $this->add();

        if (! $newId) return [];

        $attributes[static::$idFieldsName] = $newId;

        return $attributes;
    }

    protected function beforeAdd(array &$input)
    {
        parent::beforeAdd($input);

        $input['name'] = AuthManager::normalizName($input['name']);
        $input['created_at']    = time();
        $input['created_by_id'] = admin()->getId();
    }

    protected function beforeUpdate($id, array &$input)
    {
        parent::beforeUpdate($id, $input);

        $input['name'] = AuthManager::normalizName($input['name']);
        $input['modified_at'] = time();
    }

    protected function formatCreateAttributes(array $names, array &$attributes = [])
    {
        if (empty($attributes['title'])) {
            $attributes['title'] = current($names);
        }

        return $names + array_merge([
            'created_at' => time(),
            'created_by_id' => admin()->getId() ?: 0,
        ], $attributes);
    }

    /**
     * 根据用户获取权限
     *
     * @param Model $user
     * @return array
     */
    public function getForAuthority(Model $user)
    {
        $assignedAbilities = Models::table('assigned_abilities');
        $assignedRoles = Models::table('assigned_roles');

        $select =
            "{$this->tableName}.id,{$this->tableName}.`name`,{$this->tableName}.title,forbidden,ab.entity_id role_id";

        $roleType = Models::role()->getMorphType();
        $userType = $user->getMorphType();

        return $this->query()
            ->select($select)
            ->joinRaw("LEFT JOIN $assignedAbilities ab ON ({$this->tableName}.id = ab.ability_id AND ab.entity_type = $roleType)")
            ->joinRaw("LEFT JOIN $assignedRoles ar ON (ar.role_id = ab.entity_id AND ar.entity_type = $userType)")
            ->where("ar.entity_id", $user->getId())
            ->find();
    }

    public function afterUpdate($id, array &$input, $result)
    {
        parent::afterUpdate($id, $input, $result);

        // 清除所有与此权限相关的用户权限缓存
        auth()->refreshForAbility($this);
    }

    public function afterDelete($id, $result)
    {
        parent::afterDelete($id, $result);

        if ($result) {
            $this->deleteAssigned();
            // 清除所有与此权限相关的用户权限缓存
            auth()->refreshForAbility($this);
        }
    }

    /**
     * 查找出有关联的id
     *
     * @return Collection
     */
    public function findRolesIds()
    {
        if (! $id = $this->getId()) {
            return new Collection();
        }

        $type = Models::role()->getMorphType();

        $r = $this->query()
            ->from(Models::table('assigned_abilities'))
            ->where('ability_id', $id)
            ->where('entity_type', $type)
            ->find();

        return (new Collection((array) $r))->pluck('entity_id');
    }

    /**
     * 删除已分配的权限信息
     *
     * @return bool|mixed
     */
    public function deleteAssigned()
    {
        if (! $id = $this->getId()) {
            return false;
        }

        return $this->query()
            ->from(Models::table('assigned_abilities'))
            ->where('ability_id', $this->getId())
            ->delete();
    }

}
