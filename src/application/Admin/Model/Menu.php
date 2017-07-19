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

class Menu extends Model
{

    protected function beforeAdd(array & $data)
    {
        print_r($data);die;
    }

    // 保存数据前置钩子
    protected function beforeSave($id, array & $data)
    {
        if (isset($data['show'])) {
            if (! $data['show']) $data['show'] = 0;
        }
    }

    // 删除后置钩子方法，删除成功后触发
    protected function afterDelete($id, $result)
    {
        if (! $result) {
            return;
        }

        // 如果删除成功，把所有的下级菜单也一并删除
        // 以后有空再做
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
