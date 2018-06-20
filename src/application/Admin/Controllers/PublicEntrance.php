<?php
/**
 * 公开的控制器
 *
 * @author Jqh
 * @date   2017/8/1 18:12
 */

namespace Lxh\Admin\Controllers;

use Lxh\Mvc\Controller;

class PublicEntrance extends Controller
{
    // 字体展示
    public function actionFontAwesome()
    {
        return $this->content()
            ->header('Font awesome')
            ->body($this->render('font-awesome'))
            ->render();
    }
}
