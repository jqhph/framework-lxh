<?php

namespace Lxh\Auth\Database;

use Lxh\Mvc\Model;

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
     * @var bool
     */
    protected $useAuthorize = true;

    /**
     * 初始化操作
     */
    protected function initialize()
    {
        $this->useAuthorize = config('use-authorize');
    }

    /**
     * 获取可显示菜单
     *
     * @return []
     */
    public function findActive()
    {
        $ability = Models::table('ability');
        $select = $this->getDefaultSelect($ability);

        $q = $this->query()
            ->select($select)
            ->where('show', 1);

        if ($this->useAuthorize) {
            $q->leftJoin($ability, 'ability_id', "$ability.id");
        }

        return $q->find();
    }

    protected function beforeAdd(array &$input)
    {
        $input['created_at'] = $_SERVER['REQUEST_TIME'];
        $input['created_by_id'] = __admin__()->getId();

        if (empty($input['show'])) {
            $input['show'] = 0;
        }
        if ($this->useAuthorize && isset($input['ability_id'])) {
            $this->setupAbility($input);
        }
    }

    protected function setupAbility(array &$input)
    {
        $this->quickAbility = $input['quick_relate_ability'];
        unset($input['quick_relate_ability']);
        // 用户选择了权限，则以此为主
        if ($input['ability_id'] && is_numeric($input['ability_id'])) {
            return;
        }
        if (! $this->quickAbility) {
            $input['ability_id'] = 0;
            return;
        };

        $abilityName = $this->quickAbility;

        $abilityModel = Models::ability();
        $ability = $abilityModel->findOrCreate($abilityName);

        $input['ability_id'] = (int)current($ability->all())[$abilityModel->getKeyName()];
    }

    protected function getDefaultSelect($ability)
    {
        if (!$this->useAuthorize) {
            return '*';
        }

        return "{$this->tableName}.*,$ability.slug ability,$ability.title ability_title";
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
            $q = $this->query()
                ->select($select)
                ->where($this->primaryKeyName, $id);

            if ($this->useAuthorize) {
                $q->leftJoin($ability, 'ability_id', "$ability.id");
            }

            $data = $q->findOne();
            $this->attach($data);

            return $data;
        }

        $q = $this->query()
            ->select($select);
        if ($this->useAuthorize) {
            $q->leftJoin($ability, 'ability_id', "$ability.id");
        }
        return $q->find();
    }

    // 保存数据前置钩子
    protected function beforeUpdate($id, array &$input)
    {
        if (isset($input['show'])) {
            if (! $input['show']) $input['show'] = 0;
        }
        if ($this->useAuthorize && isset($input['ability_id'])) {
            $this->setupAbility($input);
        }
    }

    protected function afterUpdate($id, array &$input, $result)
    {
        // 刷新缓存
        auth()->menu()->refresh();

        if ($result) {
            operations_logger()->adminAction($this)->setUpdate()->add();
        }
    }

    protected function afterAdd($id, array &$input)
    {
        auth()->menu()->refresh();

        if ($id) {
            $this->setId($id);

            operations_logger()->adminAction($this)->setInsert()->add();
        }
    }

    protected function afterBatchDelete(array &$ids, $effect, $trash)
    {
        if (! $effect) return;

        foreach ($ids as $id) {
            // 如果删除成功，把所有的下级菜单也一并删除
            if ($ids = $this->getSubIds($id)) {
                if (! $this->query()->where('id', 'IN', $ids)->delete()) {
                    logger()->warning("删除菜单[$id]的下级菜单失败");
                }
            }
        }

        // 刷新缓存
        auth()->menu()->refresh();
        
        if ($effect) {
            $adminAction = operations_logger()->adminAction($this);

            $adminAction->input = implode(',', $ids);
            $adminAction->setBatchDelete()->add();
        }
    }

    // 删除后置钩子方法
    protected function afterDelete($id, $result, $trash)
    {
        parent::afterDelete($id, $result, $trash);

        if (! $result) return;

        // 如果删除成功，把所有的下级菜单也一并删除
        if ($ids = $this->getSubIds($id)) {
            if (! $this->query()->where('id', 'IN', $ids)->delete()) {
                logger()->warning("删除菜单[$id]的下级菜单失败");
            }
        }

        // 刷新缓存
        auth()->menu()->refresh();

        if ($trash) {
            $table = $this->trashTableName;
        } else {
            $table = $this->tableName;
        }
        $actionAdmin = operations_logger()->adminAction($this);

        $actionAdmin->table = $table;

        $actionAdmin->setDelete()->add();
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

    protected function afterToTrash($id, $result)
    {
        if ($result) {
            operations_logger()->adminAction($this)->setMoveToTrash()->add();
        }
    }

    protected function afterBatchToTrash(array $ids, $res)
    {
        if ($res) {
            $action = operations_logger()->adminAction($this);

            $action->input = implode(',', $ids);
            $action->setBatchMoveToTrash()->add();
        }
    }

    protected function afterRestore(array $ids, $res)
    {
        if ($res) {
            $action = operations_logger()->adminAction($this);

            $action->input = implode(',', $ids);
            $action->setRestore()->add();
        }
    }

}
