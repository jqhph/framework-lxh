<?php
/**
 *
 * @author Jqh
 * @date   2017-10-16 21:10:55
 */

namespace Lxh\Admin\Controllers;

//use Lxh\MVC\Controller;
use Lxh\Admin\Filter;
use Lxh\Admin\Grid;
use Lxh\Admin\Layout\Row;
use Lxh\Admin\Table\Column;
use Lxh\Admin\Table\Td;
use Lxh\Admin\Table\Th;
use Lxh\Admin\Table\Tr;
use Lxh\Http\Request;
use Lxh\Http\Response;

class Product extends Controller
{
    protected $btns = [
        'create' => 'Create'
    ];

    /**
     * 网格报表配置
     *
     * @var array
     */
    protected $grid = [
        'id' => ['show' => 0, 'sortable' => 1],
        'name' => ['sortable' => 1],
        'price' => ['sortable' => 1,],
        'counter_price',
        'share_price' => ['sortable' => 1,],
        'level',
        'stock',
        'is_hot' => ['view' => 'Boolean'],
        'is_new' => ['view' => 'Boolean'],
        'calendar' => ['view' => 'Boolean'],
        'order_num',
        'desc' => ['show' => 0],
        'category_id' => ['show' => 0],
        'created_at' => ['view' => 'Date'],
        'modified_at' => ['view' => 'Date'],
    ];

    public function initialize()
    {
    }

    public function actionList(Request $req, Response $resp, array & $params)
    {
        $content = $this->admin()->content();

        $content->header(trans(__CONTROLLER__));
        $content->description(trans(__CONTROLLER__ . ' list'));

        // 构建搜索界面
        $filter = $content->filter(function (Filter $filter) {
            $filter->multipleSelect('status')->options(range(1, 10));
            $filter->select('level')->options([1, 2]);
            $filter->text('stock')->number();
            $filter->text('name');
            $filter->text('price');
            $filter->dateRange('created_at')->between()->toTimestamp();
        });

        // 构建网格报表
        $grid = $content->grid($this->grid);
        // 添加过滤器
        $grid->filter($filter);

        $table = $grid->table();

        // 修改标题颜色
        $table->th('name', function (Th $th) {
            $th->attribute('style', 'color:blue;');
        });

        // 字段设置
        $table->field('order_num', '*****');
        $table->field('price', function ($value, $options, Td $td) {
                return $value + 100;
            });

        // 追加额外的列到最前面
        $table->prepend('序号', function (array $row, Td $td, Th $th, Tr $tr) {
            if ($tr->line() == 3) {
                // 给第三行添加active样式
                $tr->class('active');
            }

            return $tr->line();
        });

        // 增加额外的行
        $table->append('下班了', '真的嘛？！');

        $table->append('呵呵', function (array $row, Td $td, Th $th, Tr $tr) {
            // 设置标题样式
            $th->attribute('style', 'color:red;font-weight:600');
            $th->class('test-class');
            // 默认隐藏
            $th->hide();

            return '#' . $tr->line();
        });

        $table->append(function (array $row, Td $td, Th $th) {
            $th->value('叫什么好呢？');
            return '演示一下而已~';
        });

        return $content->render();
    }

    /**
     * 获取详情界面字段视图信息
     *
     * @return array
     */
    protected function makeDetailItems($id = null)
    {
        return [
            ['view' => 'varchar/edit', 'vars' => ['name' => 'name', 'labelCol' => 2, 'formCol' => 9]],
            ['view' => 'varchar/edit', 'vars' => ['name' => 'price', 'labelCol' => 2, 'formCol' => 9]],
            ['view' => 'varchar/edit', 'vars' => ['name' => 'counter_price', 'labelCol' => 2, 'formCol' => 9]],
            ['view' => 'varchar/edit', 'vars' => ['name' => 'share_price', 'labelCol' => 2, 'formCol' => 9]],
            ['view' => 'varchar/edit', 'vars' => ['name' => 'stock', 'labelCol' => 2, 'formCol' => 9]],
            ['view' => 'varchar/edit', 'vars' => ['name' => 'desc', 'labelCol' => 2, 'formCol' => 9]],
            ['view' => 'enum/edit', 'vars' => ['name' => 'level', 'labelCol' => 2, 'formCol' => 9]],
            ['view' => 'bool/edit', 'vars' => ['name' => 'is_new', 'labelCol' => 2, 'formCol' => 9]],
            ['view' => 'bool/edit', 'vars' => ['name' => 'is_hot', 'labelCol' => 2, 'formCol' => 9]],
            ['view' => 'date/edit', 'vars' => ['name' => 'calendar', 'labelCol' => 2, 'formCol' => 9]],

        ];
    }

}
