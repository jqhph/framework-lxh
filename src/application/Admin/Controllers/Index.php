<?php
/**
 *
 * @author Jqh
 * @date   2017-06-14 11:38:38
 */

namespace Lxh\Admin\Controllers;

use Lxh\Admin\Widgets\Box;
use Lxh\Admin\Widgets\Tab;
use Lxh\Admin\Widgets\Table;
use Lxh\Admin\Layout\Row;
use Lxh\Admin\Widgets\InfoBox;
use Lxh\Admin\Widgets\Navbar;
use Lxh\MVC\Controller;
use Lxh\Http\Request;
use Lxh\Http\Response;
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

        $index->addTopbarContent(<<<EOF
<li style="height:auto;"><i class="zmdi zmdi-refresh-alt"></i></li>
EOF
);

        // 触发加载首页事件
        fire(EVENT_ADMIN_INDEX, $index);

        return $index->render();
    }

    // 后台dashboard页
    public function actionDashboard()
    {
        $content = $this->admin()->content();

        $content->header(trans('Dashboard'));
        $content->description('');

        $content->row(function (Row $row) {
            $env = (new Table([], $this->environment()))->class('table-striped');

            $row->column(8, (new Box(trans('Environment'), $env))->style('inverse')->collapsable());

            $dependencies = (new Table([], $this->dependencies()))->class('table-striped');

            $row->column(4, (new Box(trans('Dependencies'), $dependencies))->style('inverse')->collapsable());
        });

        // 触发加载dashboard页事件
        fire(EVENT_ADMIN_DASHBOARD, $content);

        return $content->render();
    }

    /**
     * @return array
     */
    protected function dependencies()
    {
        $dependencies['PHP'] = '>=5.5';

        $composer = include __ROOT__ . 'kernel/composer.php';

        $dependencies = array_merge($dependencies, $composer);

        $colors = ['success', 'danger', 'primary', 'inverse', 'primary', 'pink', 'success', 'info'];
        $i = 0;
        foreach ($dependencies as $k => &$dependency) {
            if ($i > 7) {
                $i = $i % 8;
            }

            $dependency = "<span class='label label-{$colors[$i]}'>$dependency</span>";
            $i++;
        }

        return $dependencies;
    }

    /**
     * @return array
     */
    public static function environment()
    {
        $plugins = json_encode(array_keys((array)config('plugins')));

        return [
            ['name' => 'PHP version',       'value' => 'PHP/'.PHP_VERSION],
            ['name' => 'Lxh-framework version',   'value' => 'dev'],
            ['name' => 'CGI',               'value' => php_sapi_name()],
            ['name' => 'Uname',             'value' => php_uname()],
            ['name' => 'Server',            'value' => get_value($_SERVER, 'SERVER_SOFTWARE')],

            ['name' => 'Timezone',          'value' => config('timezone')],
            ['name' => 'Locale',            'value' => config('language')],
            ['name' => 'Env',               'value' => __ENV__],
            ['name' => 'Host',              'value' => request()->getUri()->getHost()],
            ['name' => 'Plugins',           'value' => "<code>{$plugins}</code>"],
        ];
    }

}
