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
     * @var Model
     */
    protected $authority;

    /**
     * The roles to be assigned.
     *
     * @var array
     */
    protected $roles;

    /**
     * 角色名或id或模型
     * 是否先重置用户和角色的关系
     *
     * @var null|array
     */
    protected $retracts = null;

    /**
     * @var bool
     */
    protected $refresh = false;

    /**
     * Constructor.
     *
     * @param \Lxh\Support\Collection|\Lxh\Auth\Database\Role|string  $roles
     */
    public function __construct(AuthManager $auth, Model $authority, $roles)
    {
        $this->auth = $auth;
        $this->authority = $authority;
        $this->roles = array_filter(Helpers::toArray($roles));
    }

    /**
     * @return $this
     */
    public function refresh()
    {
        $this->refresh = true;

        return $this;
    }

    /**
     * Assign the roles to the given authority.
     *
     * @param  Model|array|int  $authority
     * @return bool
     */
    public function then()
    {
        if ($this->roles) {
            $roles = Models::role()->findOrCreate($this->roles);
        }

        if ($this->retracts !== null) {
            $this->auth->retract($this->retracts)->then();
        }

        if ($this->roles && $roles) {
            $result = $this->assignRoles($roles, $this->authority->getId());

            $this->refresh && $this->auth->refresh();

            return $result;
        } else {
            $this->refresh && $this->auth->refresh();
        }

        return false;
    }

    /**
     * @return $this
     */
    public function retract($roles = [])
    {
        $this->retracts = &$roles;

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

        $morphType = $this->authority->getMorphType();

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
    protected function getExistingAttachRecords(Collection $roleIds, $morphType, $authorityId)
    {
        $query = $this->newPivotTableQuery()
            ->where([
                'role_id'     => ['IN', $roleIds->all()],
                'entity_id'   => $authorityId,
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
    protected function buildAttachRecords(Collection $roleIds, $morphType, $authorityId)
    {
        return $roleIds->map(function ($roleId) use ($morphType, $authorityId) {
            return [
                [
                    'role_id'     => $roleId,
                    'entity_id'   => $authorityId,
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
