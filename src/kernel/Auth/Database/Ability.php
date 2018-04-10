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

        $attributes[$this->primaryKeyName] = $newId;

        return $attributes;
    }

    protected function beforeAdd(array &$input)
    {
        parent::beforeAdd($input);

        $input['name'] = AuthManager::normalizName($input['name']);
        $input['created_at']    = time();
        $input['created_by_id'] = __admin__()->getId();
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
            'created_by_id' => __admin__()->getId() ?: 0,
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
        $assignedRoles     = Models::table('assigned_roles');

        $roleType = Models::role()->getMorphType();
        $userType = $user->getMorphType();

        $roles = $this->query()
            ->from($assignedRoles)
            ->select('role_id')
            ->where('entity_id', $user->getId())
            ->where('entity_type', $userType)
            ->find();

        if (! $roles) return [];
        
        $roles = new Collection($roles);

        $select =
            "{$this->tableName}.id,{$this->tableName}.`name`,{$this->tableName}.title,forbidden,ab.entity_id role_id";

        return $this->query()
            ->select($select)
            ->joinRaw("LEFT JOIN $assignedAbilities ab ON ({$this->tableName}.id = ab.ability_id AND ab.entity_type = $roleType)")
            ->whereIn('ab.entity_id', $roles->pluck('role_id')->all())
            ->find();
    }

    public function afterAdd($insertId, array &$input)
    {
        parent::afterAdd($insertId, $input);

        if ($insertId) {
            $this->setId($insertId);

            operations_logger()->adminAction($this)->setInsert()->add();
        }
    }

    public function afterUpdate($id, array &$input, $result)
    {
        parent::afterUpdate($id, $input, $result);

        // 清除所有与此权限相关的用户权限缓存
        auth()->refreshForAbility($this);

        if ($result) {
            operations_logger()->adminAction($this)->setUpdate()->add();
        }
    }

    public function afterDelete($id, $result, $trash)
    {
        parent::afterDelete($id, $result, $trash);

        if ($result) {
            $this->deleteAssigned();
            // 清除所有与此权限相关的用户权限缓存
            auth()->refreshForAbility($this);

            if ($trash) {
                $table = $this->trashTableName;
            } else {
                $table = $this->tableName;
            }
            $actionAdmin = operations_logger()->adminAction($this);

            $actionAdmin->table = $table;

            $actionAdmin->setDelete()->add();
        }
    }

    public function afterBatchDelete(array &$ids, $effect, $trash)
    {
        parent::afterBatchDelete($ids, $effect, $trash);

        if ($effect) {
            $adminAction = operations_logger()->adminAction($this);

            $adminAction->input = implode(',', $ids);
            $adminAction->setBatchDelete()->add();
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

    protected function afterToTrash($id, $result)
    {
        if ($result) {
            operations_logger()->adminAction($this)->setMoveToTrash()->add();
        }
    }

    protected function afterBatchToTrash(array $ids, $res)
    {
        if ($res) {
            $action = operations_logger()->adminAction($this);

            $action->input = implode(',', $ids);
            $action->setBatchMoveToTrash()->add();
        }
    }

    protected function afterRestore(array $ids, $res)
    {
        if ($res) {
            $action = operations_logger()->adminAction($this);

            $action->input = implode(',', $ids);
            $action->setRestore()->add();
        }
    }

}
