<?php
/**
 * Created by PhpStorm.
 * User: Jqh
 * Date: 2017/8/1
 * Time: 20:58
 */

namespace Lxh\Admin\Models;

use Lxh\Auth\AuthManager;
use Lxh\Auth\Database\Models;

class Role extends \Lxh\Auth\Database\Role
{
    /**
     * @var array
     */
    protected $selectFields = ['id', 'name', 'created_at', 'modified_at', 'comment', 'title'];

    /**
     * @var array
     */
    protected $abilities = [];

    public function beforeUpdate($id, array &$input)
    {
        $data['modified_at'] = time();

        $this->abilities = $input['abilities'];
        unset($input['abilities']);
    }

    public function afterUpdate($id, array &$input, $result)
    {
        if ($this->abilities) {
            $this->resetAbilities();
            $this->assignAbilities($this->abilities);
            // 清除相关用户缓存
            auth()->refreshForRole($this);
        }
    }

    public function afterDelete($id, $result)
    {
        if (! $result) return;

        $this->resetAbilities();
        auth()->refreshForRole($this);
    }

    public function beforeAdd(array &$input)
    {
        $data['created_at']    = time();
        $data['created_by_id'] = admin()->id;

        $this->abilities = $input['abilities'];
        unset($input['abilities']);
    }

    public function afterAdd($insertId, array &$input)
    {
        if (! $insertId) return;

        if ($this->abilities) {
            $this->assignAbilities($this->abilities);
            // 清除相关用户缓存
            auth()->refreshForRoles($this);
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
            ->select(['id', 'name', 'created_at', 'modified_at', 'admin.username AS created_by', 'comment', 'title'])
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
