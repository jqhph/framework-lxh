<?php
/**
 * Created by PhpStorm.
 * User: Jqh
 * Date: 2017/8/1
 * Time: 20:58
 */

namespace Lxh\Admin\Models;

class Role extends \Lxh\Auth\Database\Role
{
    protected $selectFields = ['id', 'name', 'created_at', 'modified_at', 'admin.username AS created_by', 'comment', 'title'];

    public function beforeSave($id, array & $data)
    {
        $data['modified_at'] = time();
    }

    public function afterSave($id, array &$input, $result)
    {
        if ($input['abilities']) {
            $this->resetAbilities();
            $this->assignAbilities($input['abilities']);
            // 清除相关用户缓存
            auth()->refreshForRoles($this);
        }
    }

    public function afterDelete($id, $result)
    {
        if (! $result) return;

        $this->resetAbilities();
        auth()->refreshForRoles($this);
    }

    public function beforeAdd(array &$input)
    {
        $data['created_at']    = time();
        $data['created_by_id'] = admin()->id;
    }

    public function afterAdd($insertId, array &$input)
    {
        if (! $insertId) return;

        if ($input['abilities']) {
            $this->assignAbilities($input['abilities']);
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
            ->select($this->selectFields)
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

    // 查找数据
    public function find()
    {
        $id = $this->{$this->idFieldsName};

        if ($id) {
            $data = $this->query()->select($this->selectFields)->leftJoin('admin', 'admin.id', 'created_by_id')->where($this->idFieldsName, $id)->findOne();
            $this->fill($data);
            return $data;
        }
        return $this->query()->select($this->selectFields)->leftJoin('admin', 'admin.id', 'created_by_id')->where('deleted', 0)->find();
    }
}
