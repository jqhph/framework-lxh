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
}
