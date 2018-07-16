<?php
/**
 *
 * @author Jqh
 * @date   2017-06-14 11:38:38
 */

namespace Lxh\Admin\Controllers;

use Lxh\Admin\Widgets\Box;
use Lxh\Admin\Widgets\Table;
use Lxh\Admin\Layout\Row;
use Lxh\Application;
use Lxh\Coroutine\Scheduler;
use Lxh\Coroutine\SystemCall;
use Lxh\Coroutine\Task;
use function Lxh\Coroutine\task;
use function Lxh\Coroutine\value;
use function Lxh\Coroutine\kill;
use function Lxh\Coroutine\id;
use Lxh\Mvc\Controller;
use Overtrue\EasySms\EasySms;
use Overtrue\EasySms\Exceptions\NoGatewayAvailableException;

class Index extends Controller
{
    protected function initialize()
    {
    }

    // 主页菜单
    public function actionIndex()
    {
        $index = $this->admin()->index();

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

            $row->column(8, (new Box(trans('Environment'), $env))->collapsable());

            $dependencies = (new Table([], $this->dependencies()))->class('table-striped');

            $row->column(4, (new Box(trans('Dependencies'), $dependencies))->collapsable());
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

        $composer = include __ROOT__ . '/kernel/composer.php';

        $dependencies = array_merge($dependencies, $composer);

        foreach ($dependencies as $k => &$dependency) {
            $dependency = "<span class='label label-primary'>$dependency</span>";
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
            ['name' => 'PHP version',           'value' => 'PHP/'.PHP_VERSION],
            ['name' => 'Lxh Framework version', 'value' => 'dev'],
            ['name' => 'CGI',                   'value' => php_sapi_name()],
            ['name' => 'Uname',                 'value' => php_uname()],
            ['name' => 'Server',                'value' => getvalue($_SERVER, 'SERVER_SOFTWARE')],

            ['name' => 'Timezone',              'value' => config('timezone')],
            ['name' => 'Locale',                'value' => config('locale')],
            ['name' => 'Env',                   'value' => __ENV__],
            ['name' => 'Host',                  'value' => request()->getUri()->getHost()],
            ['name' => 'Plugins',               'value' => "<code>{$plugins}</code>"],
        ];
    }

    public function actionTest(array $params)
    {
        try {
            $scheduler = new Scheduler();

            echo '<pre>';

            require Application::getAlias('@root/application/Coroutine/helpers.php');

            $scheduler->task(
                $this->task4()
            );

//            $scheduler->task(
//                $this->task()
//            );

//            $scheduler->task(
//                $this->task()
//            );


            $scheduler->run();

            return $params;
        } catch(\Exception $e) {
            var_dump($e->getMessage());
        }


    }

    function childTask() {
        $tid = (yield id());
        while (true) {
            echo "Child task $tid still alive!\n";
            yield;
        }
    }

    function echoTimes($msg, $max) {
        $tid = (yield id());
        for ($i = 1; $i <= $max; ++$i) {
            echo "[$tid]$msg iteration $i\n";
            yield;
        }

        yield \Lxh\Coroutine\value("[$tid][$msg - $max]");
    }

    function task() {
        $ret = yield $this->echoTimes('foo', 10);

        var_dump($ret);

        echo "---\n";
        yield $this->echoTimes('bar', 5);

        yield; // force it to b e a coroutine
    }


    function task4() {
        try {
            $tid = (yield id());
            $childTid = (yield task($this->childTask()));
            for ($i = 1; $i <= 6; ++$i) {
                echo "Parent task $tid iteration $i.\n";
                yield;

                if ($i == 3) {
                    yield kill(99);
                }
            }
        } catch (\Exception $e) {
            ddd(123,$e->getMessage());
        }

    }


    function task1() {
        $tid = (yield id());
        for ($i = 1; $i <= 10; ++$i) {
            echo "This is task [$tid] iteration $i.\n";
            yield;
        }
    }

    function task2() {
        $tid = (yield id());
        for ($i = 1; $i <= 5; ++$i) {
            echo "This is task [$tid] iteration $i.\n";
            yield;
        }
    }

}
