<?php

namespace Lxh\Auth\Database;

use Lxh\Auth\AuthManager;
use Lxh\MVC\Model;

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
        $input['name'] = AuthManager::normalizName($input['name']);
        $input['created_at']    = time();
        $input['created_by_id'] = admin()->getId();
    }

    protected function beforeUpdate($id, array &$input)
    {
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
}
