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

        $attributes[static::$idFieldsName] = $newId;
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
    public function assignAbilities($abilities)
    {
        if (!($id = $this->getId()) || ! $abilities) {
            return false;
        }
        $type = $this->getMorphType();
        $inserts = [];

        foreach (array_filter((array) $abilities) as &$abilityId) {
            $inserts[] = [
                'ability_id' => $abilityId,
                'entity_id' => $id,
                'entity_type' => $type
            ];
        }

        if (!$inserts) return false;

        return query()->from(Models::table('assigned_abilities'))->batchInsert($inserts);
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

    public function beforeUpdate($id, array &$input)
    {
        $data['modified_at'] = time();

        $this->abilities = $input['abilities'];
        unset($input['abilities']);
    }

    public function afterUpdate($id, array &$input, $result)
    {
        $this->resetAbilities();
        $this->assignAbilities($this->abilities);
        // 清除相关用户缓存
        auth()->refreshForRole($this);
    }

    public function afterDelete($id, $result)
    {
        if (! $result) return;

        $this->resetAbilities();
        auth()->refreshForRole($this);
    }

    public function beforeAdd(array &$input)
    {
        $input['created_at']    = time();
        $input['created_by_id'] = admin()->getId();

        $this->abilities = $input['abilities'];
        unset($input['abilities']);
    }

    public function afterAdd($insertId, array &$input)
    {
        if (! $insertId) return;

        if ($this->abilities) {
            $this->assignAbilities($this->abilities);
            // 清除相关用户缓存
            auth()->refreshForRole($this);
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
            ->select(['id', 'name', 'created_at', 'modified_at', 'admin.username AS created_by', 'comment', 'title', 'created_by_id'])
            ->leftJoin('admin', 'admin.id', 'created_by_id')
            ->limit($offset, $maxSize);

        if ($where) {
            $q->where($where);
        }

        if ($orderString) {
            $q->sort($orderString);
        }

        return $q->find();
    }

    public function find()
    {
        $data = parent::find(); // TODO: Change the autogenerated stub

        if (! $data || ! $this->getId()) return $data;

        $data['abilities'] = $this->findAbilitiesIdsForRole()->all();

        return $data;
    }

}
