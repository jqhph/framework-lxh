<?php
/**
 * 公开的控制器
 *
 * @author Jqh
 * @date   2017/8/1 18:12
 */

namespace Lxh\Admin\Controller;

use Lxh\MVC\Controller;

class PublicEntrance extends Controller
{
    // 字体展示
    public function actionFontAwesome()
    {
        $navTitle = 'Font awesome';

        $this->share('navTitle', $navTitle);

        return $this->render('font-awesome', [], true);
    }
}
