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

class Index extends Controller
{
    protected function initialize()
    {
    }

    // 主页菜单
    public function actionList()
    {
        // 关闭输出控制台调试信息
        $this->withConsoleOutput(false);

        $index = $this->admin()->index();

        $index->addTopbarContent(<<<EOF
<li>
    <div class="notification-box">
        <ul class="list-inline m-b-0">
            <li>
                <a href="javascript:void(0);" class="right-bar-toggle"><i class="zmdi zmdi-notifications-none"></i></a>
                <div class="noti-dot" style="display:none">
                    <span class="dot"></span><span class="pulse"></span>
                </div>
            </li>
        </ul>
    </div>
</li>
EOF
);

        return $index->render();



//        return $this->render('public.public', ['homeUrl' => '/admin/index/index', 'imview' => &$chat]);
    }

    // 首页
    public function actionIndex()
    {
        $content = $this->admin()->content();

        $content->row(function (Row $row) {
            $row->column(4, new InfoBox(
                'Messages', 'users', 'success', '/admin/messages', '8', ['badge' => '4%', 'label' => 'Message Total'])
            );
            $row->column(4, new InfoBox(
                'Dialogue', 'shopping-cart', 'danger', '/admin/dialogue', '700', ['badge' => '14%', 'label' => 'Dialogue Total'])
            );
            $row->column(4, new InfoBox(
                'Users', 'book', 'info', '/admin/users', '2786', ['badge' => '56%', 'label' => 'Users Total']));
        });


        return $content->render();
//        return $this->render('index', [], true);
    }

}
