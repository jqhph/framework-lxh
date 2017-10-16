<?php
/**
 * 菜单模型
 *
 * @author Jqh
 * @date   2017/7/19 10:51
 */

namespace Lxh\Admin\Model;

use Lxh\Helper\Entity;
use Lxh\MVC\Model;
use Lxh\Contracts\Container\Container;

class Menu extends Model
{
    /**
     * 获取可显示菜单
     *
     * @return []
     */
    public function findShow()
    {
        return $this->query()->where(['deleted' => 0, 'show' => 1])->read();
    }

    protected function beforeAdd(array & $data)
    {
        $data['created_at'] = $_SERVER['REQUEST_TIME'];

        $data['created_by_id'] = admin()->id;

        if (empty($data['show'])) {
            $data['show'] = 0;
        }

    }

    // 保存数据前置钩子
    protected function beforeSave($id, array & $data)
    {
        if (isset($data['show'])) {
            if (! $data['show']) $data['show'] = 0;
        }
    }

    protected function afterAdd($insertId, array & $data)
    {
        if (! $insertId) return;

//        $this->events->fire('Menu.add');
    }

    protected function afterSave($id, array & $data, $result)
    {
        if (! $result) return;

//        $this->events->fire('Menu.save');
    }

    // 删除后置钩子方法
    protected function afterDelete($id, $result)
    {
        if (! $result) return;

        // 如果删除成功，把所有的下级菜单也一并删除
        if ($ids = $this->getSubIds($id)) {
            if (! $this->query()->where('id', 'IN', $ids)->delete()) {
                logger()->warning("删除菜单[$id]的下级菜单失败");
            }
        }
    }

    /**
     * 获取所有下级菜单id数组
     *
     * @param  int $parentId
     * @return array
     */
    public function getSubIds($parentId)
    {
        $rows = $this->query()->select('id')->where('parent_id', $parentId)->find();

        $result = [];

        if (! $rows) {
            return $result;
        }

        foreach ($rows as & $row) {
            $ids = $this->getSubIds($row['id']);

            $ids[] = $row['id'];

            $result = array_merge($result, $ids);
        }

        return $result;
    }

    // 判断菜单是否是系统菜单
    public function isSystem($id)
    {
        $r = $this->query()->select('type')->where($this->idFieldsName, $id)->findOne();

        if (! $r) {
            return false;
        }
        return $r['type'] == 2 ? true : false;
    }
}
