<?php
/**
 * Created by PhpStorm.
 * User: Jqh
 * Date: 2017/8/1
 * Time: 20:58
 */

namespace Lxh\Admin\Models;

use Lxh\Kernel\Model\Record;

class Role extends Record
{
    protected $selectFields = ['id', 'name', 'created_at', 'modified_at', 'admin.username AS created_by'];

    protected $permissions;

    public function beforeSave($id, array & $data)
    {
        // 权限数组
        $this->permissions = $data['permissions'];
        unset($data['permissions']);

        $data['modified_at'] = time();
    }

    public function afterSave($id, array & $data, $result)
    {
        if (! $result) return;

        $this->removePermissions($id);

        if (! $this->addPermissions($id)) {
            logger()->warning("修改角色成功[$id]，更新权限数据失败");
        }
    }

    public function afterDelete($id, $result)
    {
        if (!$result) return;

        $this->removePermissions($id);
    }

    public function beforeAdd(array & $data)
    {
        // 权限数组
        $this->permissions = $data['permissions'];
        unset($data['permissions']);

        $data['created_at']    = time();
        $data['created_by_id'] = admin()->id;
    }

    public function afterAdd($insertId, array & $data)
    {
        if (! $insertId) return;

        if (! $this->addPermissions($insertId)) {
            logger()->warning("创建角色成功[$insertId]，更新权限数据失败");
        }
    }

    // 删除中间表权限数据
    protected function removePermissions($roleId)
    {
        return query()->from('role_menu')->where('role_id', $roleId)->delete();
    }

    // 更新权限数据
    protected function addPermissions($roleId)
    {
        // 新增成功
        $data = [];
        foreach ($this->permissions as & $menuId) {
            if (! $menuId) continue;
            $data[] = ['role_id' => $roleId, 'menu_id' => intval($menuId)];
        }

        // 插入中间表
        return pdo()->batchAdd('role_menu', $data);
    }

    /**
     * 获取角色关联的权限数据
     *
     * @param  int $roleId
     * @return []
     */
    public function getPermissions($roleId)
    {
        $data = query()->from('role_menu')->select('menu_id')->where('role_id', $roleId)->find();

        $p = ['menus' => [], 'custom' => []];

        if (! $data) {
            return $p;
        }

        foreach ($data as & $r) {
            $p['menus'][] = $r['menu_id'];
        }

        return $p;
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
