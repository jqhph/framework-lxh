<?php
/**
 * 菜单模型
 *
 * @author Jqh
 * @date   2017/7/19 10:51
 */

namespace Lxh\Admin\Models;

use Lxh\Auth\Database\Models;
use Lxh\Helper\Entity;
use Lxh\Helper\Util;
use Lxh\MVC\Model;
use Lxh\Contracts\Container\Container;

class Menu extends Model
{
    /**
     * 快捷关联的权限
     *
     * @var string
     */
    protected $quickAbility = '';

    /**
     * 下拉选框选择的权限
     *
     * @var string
     */
    protected $selectedAbility = '';

    /**
     * 获取可显示菜单
     *
     * @return []
     */
    public function findShow()
    {
        return $this->query()->where(['deleted' => 0, 'show' => 1])->read();
    }

    protected function beforeAdd(array &$input)
    {
        $input['created_at'] = $_SERVER['REQUEST_TIME'];

        $input['created_by_id'] = admin()->getId();

        if (empty($input['show'])) {
            $input['show'] = 0;
        }

        $this->setupAbility($input);
    }

    protected function setupAbility(array &$input)
    {
        $this->quickAbility = $input['quick_relate_ability'];
        $this->selectedAbility = $input['ability'];
        unset($input['quick_relate_ability'], $input['ability']);

        // 用户选择了权限，则以此为主
        if ($this->selectedAbility && is_int($this->selectedAbility)) {
            return $input['ability_id'] = $this->selectedAbility;
        }
        if (! $this->quickAbility) return;

//        $controller = Util::convertWith($input['controller'], true, '-');
        $abilityName = $this->quickAbility;

        $abilityModel = Models::ability();
        $ability = $abilityModel->findOrCreate($abilityName);

        $input['ability_id'] = current($ability->all())[$abilityModel->getKeyName()];
    }

    // 保存数据前置钩子
    protected function beforeSave($id, array &$input)
    {
        if (isset($input['show'])) {
            if (! $input['show']) $input['show'] = 0;
        }
        $this->setupAbility($input);
    }

    protected function afterAdd($insertId, array & $input)
    {
        if (! $insertId) return;
    }

    protected function afterSave($id, array & $input, $result)
    {
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
