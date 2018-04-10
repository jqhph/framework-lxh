<?php

namespace Lxh\Auth\Database;

use Lxh\MVC\Model;
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

    /**
     * @var array
     */
    protected $abilities = [];

    protected function initialize()
    {
        $this->tableName = Models::table('role');
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

        $attributes[$this->primaryKeyName] = $newId;
        return $attributes;
    }

    protected function formatCreateAttributes(array &$attributes = [])
    {
        return array_merge([
            'created_at' => time(),
            'created_by_id' => __admin__()->getId()
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


    /**
     * 重置角色已分配的权限
     *
     * @return bool
     */
    public function resetAbilities()
    {
        if (! $id = $this->getId()) {
            return false;
        }

        $where = [
            'entity_id' => $id,
            'entity_type' => $this->getMorphType()
        ];

        return query()->from(Models::table('assigned_abilities'))->where($where)->delete();
    }

    /**
     * 给角色分派权限
     *
     * @param mixed $abilities 权限id
     * @return bool|mixed
     */
    public function assignAbilities(array $abilities)
    {
        if (!($id = $this->getId()) || ! $abilities) {
            return false;
        }
        $type = $this->getMorphType();
        $inserts = [];

        foreach ($abilities as &$abilityId) {
            $inserts[] = [
                'ability_id'  => $abilityId,
                'entity_id'   => $id,
                'entity_type' => $type
            ];
        }

        if (!$inserts) return false;

        return query()->from(Models::table('assigned_abilities'))->batchReplace($inserts);
    }

    /**
     * 获取角色所属用户id数组
     *
     * @return Collection
     */
    public function findUsersIds()
    {
        if (!$id = $this->getId()) {
            return new Collection();
        }

        $usertype = Models::user()->getMorphType();

        $where = [
            'role_id' => $id,
            'entity_type' => $usertype
        ];
        $content = query()->select('entity_id')->from(Models::table('assigned_roles'))->where($where)->find();

        return (new Collection((array)$content))->pluck('entity_id');
    }

    /**
     * 根据角色获取权限id
     *
     * @return Collection
     */
    public function findAbilitiesIdsForRole()
    {
        if (! $id = $this->getId()) {
            return new Collection();
        }

        $where = [
            'entity_type' => $this->getMorphType(),
            'entity_id' => $id
        ];

        $r = query()->select('ability_id')->from(Models::table('assigned_abilities'))->where($where)->find();

        return (new Collection((array)$r))->pluck('ability_id');
    }

    /**
     * 根据角色获取权限
     *
     * @return Collection
     */
    public function findAbilitiesForRole()
    {
        if (! $id = $this->getId()) {
            return new Collection();
        }

        $ability = Models::table('ability');

        $where = [
            'entity_type' => $this->getMorphType(),
            'entity_id' => $id
        ];

        $r = query()
            ->select("ability_id,$ability.name,$ability.title")
            ->from(Models::table('assigned_abilities'))
            ->leftJoin($ability, "$ability.id", 'assigned_abilities.ability_id')
            ->where($where)
            ->find();

        return (new Collection($r));
    }

    public function beforeUpdate($id, array &$input)
    {
        $data['modified_at'] = time();

        $this->abilities = array_filter(explode(',', $input['abilities']));
        unset($input['abilities']);
    }

    public function afterUpdate($id, array &$input, $result)
    {
        parent::afterUpdate($id, $input, $result);

        $this->resetAbilities();
        $this->assignAbilities($this->abilities);
        // 清除相关用户缓存
        auth()->refreshForRole($this);

        if ($result) {
            operations_logger()->adminAction($this)->setUpdate()->add();
        }
    }

    public function afterDelete($id, $result, $trash)
    {
        parent::afterDelete($id, $result, $trash);

        if (! $result) return;

        $this->resetAbilities();
        auth()->refreshForRole($this);

        // 保存操作日志
        if ($trash) {
            $table = $this->trashTableName;
        } else {
            $table = $this->tableName;
        }
        $actionAdmin = operations_logger()->adminAction($this);

        $actionAdmin->table = $table;

        $actionAdmin->setDelete()->add();
    }

    public function beforeAdd(array &$input)
    {
        parent::beforeAdd($input);

        $input['created_at']    = time();
        $input['created_by_id'] = __admin__()->getId();

        $this->abilities = $input['abilities'];
        unset($input['abilities']);
    }

    public function afterAdd($insertId, array &$input)
    {
        parent::afterAdd($insertId, $input);
        
        if (! $insertId) return;

        if ($this->abilities) {
            $this->assignAbilities($this->abilities);
            // 清除相关用户缓存
            auth()->refreshForRole($this);
        }

        operations_logger()->adminAction($this)->setInsert()->add();
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
     * 获取列表页数据
     *
     * @param  array | string $where
     * @param  int $offset
     * @param  int $maxSize
     * @param  string $orderString
     * @return array
     */
    public function findList(array $where, $orderString = 'id Desc', $offset = 0, $maxSize = 20)
    {
        $q = $this->query()
            ->select(['id', 'name', 'created_at', 'modified_at', 'comment', 'title', 'created_by_id'])
            ->joinRaw("LEFT JOIN assigned_roles ar ON ({$this->tableName}.id = ar.role_id AND ar.entity_type = 1)")
            ->limit($offset, $maxSize);

        if ($where) {
            $q->where($where);
        }

        if ($orderString) {
            $q->sort($orderString);
        }

        return $q->find();
    }

    public function count(array $where = [])
    {
        $q = $this->query()
            ->joinRaw("LEFT JOIN assigned_roles ar ON ({$this->tableName}.id = ar.role_id AND ar.entity_type = 1)");

        if ($where) $q->where($where);

        return $q->count();
    }

    public function find()
    {
        $data = parent::find(); // TODO: Change the autogenerated stub

        if (! $data || ! $this->getId()) return $data;

        $data['abilities'] = $this->findAbilitiesIdsForRole()->all();

        return $data;
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
