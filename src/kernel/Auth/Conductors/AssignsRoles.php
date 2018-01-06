<?php

namespace Lxh\Auth\Conductors;

use Lxh\Auth\AuthManager;
use Lxh\Auth\Helpers;
use Lxh\ORM\Query;
use Lxh\Support\Collection;
use Lxh\Auth\Database\Models;
use Lxh\MVC\Model;

class AssignsRoles
{
    /**
     * @var AuthManager
     */
    protected $auth;

    /**
     * The roles to be assigned.
     *
     * @var array
     */
    protected $roles;

    /**
     * 是否先重置用户和角色的关系
     *
     * @var bool
     */
    protected $reset = false;

    /**
     * Constructor.
     *
     * @param \Lxh\Support\Collection|\Lxh\Auth\Database\Role|string  $roles
     */
    public function __construct(AuthManager $auth, $roles)
    {
        $this->auth = $auth;
        $this->roles = Helpers::toArray($roles);
    }

    /**
     * Assign the roles to the given authority.
     *
     * @param  Model|array|int  $authority
     * @return bool
     */
    public function then()
    {
        $authority = $this->auth->user();

        $roles = Models::role()->findOrCreate($this->roles);

        return $this->assignRoles($roles, $authority->getId());
    }

    /**
     * @return $this
     */
    public function reset()
    {
        $this->reset = true;

        return $this;
    }

    /**
     * Assign the given roles to the given authorities.
     *
     * @param  \Lxh\Support\Collection  $roles
     * @param  string $authorityClass
     * @param  int  $authorityId
     * @return bool
     */
    protected function assignRoles(Collection $roles, $authorityId)
    {
        $roleIds = $roles->map(function ($model) {
            return $model['id'];
        });

        $morphType = $this->auth->user()->getMorphType();

        $records = $this->buildAttachRecords($roleIds, $morphType, $authorityId);

        $existing = $this->getExistingAttachRecords($roleIds, $morphType, $authorityId);

        return $this->createMissingAssignRecords($records, $existing);
    }

    /**
     * Get the pivot table records for the roles already assigned.
     *
     * @param  \Lxh\Support\Collection  $roleIds
     * @param  string $morphType
     * @param  int  $authorityId
     * @return \Lxh\Support\Collection
     */
    protected function getExistingAttachRecords($roleIds, $morphType, $authorityId)
    {
        $query = $this->newPivotTableQuery()
            ->where([
                'role_id' => ['IN', $roleIds->all()],
                'entity_id' => $authorityId,
                'entity_type' => $morphType
            ]);

        return new Collection($query->find());
    }

    /**
     * Build the raw attach records for the assigned roles pivot table.
     *
     * @param  \Lxh\Support\Collection  $roleIds
     * @param  string $morphType
     * @param  int $authorityId
     * @return \Lxh\Support\Collection
     */
    protected function buildAttachRecords($roleIds, $morphType, $authorityId)
    {
        return $roleIds->map(function ($roleId) use ($morphType, $authorityId) {
            return [
                [
                    'role_id' => $roleId,
                    'entity_id' => $authorityId,
                    'entity_type' => $morphType,
                ]
            ];
        })->collapse();
    }

    /**
     * Save the non-existing attach records in the DB.
     *
     * @param  \Lxh\Support\Collection  $records
     * @param  \Lxh\Support\Collection  $existing
     * @return bool
     */
    protected function createMissingAssignRecords(Collection $records, Collection $existing)
    {
        $existing = $existing->keyBy(function ($record) {
            return $this->getAttachRecordHash((array) $record);
        });

        $records = $records->reject(function ($record) use ($existing) {
            return $existing->has($this->getAttachRecordHash((array) $record));
        });

        $records = $records->all();

        if ($this->reset()) {
            $user = $this->auth->user();
            $where = [
                'entity_id' => $user->getId(),
                'entity_type' => $user->getMorphType()
            ];
            $this->newPivotTableQuery()->where($where)->delete();
        }

        return $records ? $this->newPivotTableQuery()->batchInsert($records) : false;
    }

    /**
     * Get a string identifying the given attach record.
     *
     * @param  array  $record
     * @return string
     */
    protected function getAttachRecordHash(array $record)
    {
        return $record['role_id'].$record['entity_id'].$record['entity_type'];
    }

    /**
     * Get a query builder instance for the assigned roles pivot table.
     *
     * @return Query
     */
    protected function newPivotTableQuery()
    {
        return query()->from(Models::table('assigned_roles'));
    }
}
