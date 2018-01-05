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

    protected $permissions;

    public function beforeSave($id, array & $data)
    {
        $data['modified_at'] = time();
    }

    public function afterSave($id, array & $data, $result)
    {
    }

    public function afterDelete($id, $result)
    {
    }

    public function beforeAdd(array & $data)
    {
        $data['created_at']    = time();
        $data['created_by_id'] = admin()->id;
    }

    public function afterAdd($insertId, array & $data)
    {
        if (! $insertId) return;
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
