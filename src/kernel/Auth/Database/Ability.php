<?php

namespace Lxh\Auth\Database;

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

        $newId = $this->query()->insert($attributes);

        if (! $newId) return [];

        $attributes[$this->idFieldsName] = $newId;

        return $attributes;
    }

    protected function formatCreateAttributes(array $names, array &$attributes = [])
    {
        if (empty($attributes['title'])) {
            $attributes['title'] = current($names);
        }

        return $names + array_merge([
            'created_at' => time(),
        ], $attributes);
    }

    public function getForAuthority(Model $user)
    {
        $assignedAbilities = Models::table('assigned_abilities');
        $assignedRoles = Models::table('assigned_roles');

        $select =
            "{$this->tableName}.id,{$this->tableName}.name,forbidden,$assignedAbilities.entity_id role_id";

/**
SELECT
	abilities.id,abilities.name,forbidden,assigned_abilities.entity_id AS role_id
FROM
	`abilities`
LEFT JOIN `assigned_abilities` ON assigned_abilities.entity_id = `abilities`.`id`
LEFT JOIN `assigned_roles` ON assigned_abilities.entity_id = assigned_roles.entity_id
WHERE
	assigned_abilities.entity_type = 2
    AND assigned_roles.entity_type = 1
    AND assigned_roles.entity_id = 1
*/
        return $this->query()
            ->select($select)
            ->join($assignedAbilities, 'id', "$assignedAbilities.entity_id")
            ->join($assignedRoles, "$assignedAbilities.entity_id", "$assignedRoles.entity_id")
            ->where("$assignedRoles.entity_id", $user->getId())
            ->where($assignedAbilities.'.entity_type', Models::role()->getMorphType())
            ->where($assignedRoles.'.entity_type', $user->getMorphType())
            ->find();
    }
}
