<?php
/**
 *
 * @author Jqh
 * @date   2017-06-14 11:38:38
 */

namespace Lxh\Admin\Controllers;

use Lxh\Chats\Views\Room;
use Lxh\Admin\Layout\Row;
use Lxh\Admin\Widgets\InfoBox;
use Lxh\Admin\Widgets\Navbar;
use Lxh\MVC\Controller;
use Lxh\Http\Request;
use Lxh\Http\Response;
use Lxh\Plugins\Dispatcher;
use Lxh\Support\Composer;

class Index extends Controller
{
    protected function initialize()
    {
    }

    // 主页菜单
    public function actionIndex()
    {
        // 关闭输出控制台调试信息
        $this->withConsoleOutput(false);

        $index = $this->admin()->index();

        // 触发加载首页事件
        fire(EVENT_ADMIN_INDEX, $index);

        return $index->render();
    }

    // 后台dashboard页
    public function actionDashboard()
    {
        $content = $this->admin()->content();

        // 触发加载dashboard页事件
        fire(EVENT_ADMIN_DASHBOARD, $content);

        return $content->render();
    }

}
