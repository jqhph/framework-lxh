<?php

namespace Lxh\Auth\Database;

use Lxh\Auth\Database\Models;
use Lxh\MVC\Model;

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
        $ability = Models::table('ability');
        $select = $this->getDefaultSelect($ability);

        return $this->query()
            ->select($select)
            ->where('deleted', 0)
            ->where('show', 1)
            ->leftJoin($ability, 'ability_id', "$ability.id")
            ->find();
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
        unset($input['quick_relate_ability']);

        // 用户选择了权限，则以此为主
        if ($input['ability_id'] && is_int($input['ability_id'])) {
            return;
        }
        if (! $this->quickAbility) return;

//        $controller = Util::convertWith($input['controller'], true, '-');
        $abilityName = $this->quickAbility;

        $abilityModel = Models::ability();
        $ability = $abilityModel->findOrCreate($abilityName);

        $input['ability_id'] = current($ability->all())[$abilityModel->getKeyName()];
    }

    protected function getDefaultSelect($ability)
    {
        return "{$this->tableName}.*,$ability.name ability,$ability.title ability_title";
    }

    /**
     * 查找数据方法
     *
     * @return array
     */
    public function find()
    {
        $id = $this->getId();

        $ability = Models::table('ability');
        $select = $this->getDefaultSelect($ability);

        if ($id) {
            $data = $this->query()
                ->select($select)
                ->where(static::$idFieldsName, $id)
                ->leftJoin($ability, 'ability_id', "$ability.id")
                ->where('deleted', 0)
                ->findOne();
            $this->attach($data);

            return $data;
        }
        return $this->query()
            ->select($select)
            ->where('deleted', 0)
            ->leftJoin($ability, 'ability_id', "$ability.id")
            ->find();
    }

    // 保存数据前置钩子
    protected function beforeUpdate($id, array &$input)
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

    protected function afterUpdate($id, array & $input, $result)
    {
        if ($id) {
            // 刷新缓存
            auth()->menu()->refresh();
        }
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

        // 刷新缓存
        auth()->menu()->refresh();
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
        $r = $this->query()->select('type')->where(static::$idFieldsName, $id)->findOne();

        if (! $r) {
            return false;
        }
        return $r['type'] == 2 ? true : false;
    }
}
