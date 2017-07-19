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
    // 保存数据前置钩子
    protected function beforeSave($id, array & $data)
    {
        if (isset($data['show'])) {
            if (! $data['show']) $data['show'] = 0;
        }
    }
}
