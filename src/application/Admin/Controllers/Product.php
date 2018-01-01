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
use Lxh\Admin\Table\Table;
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

        // 添加过滤器，过滤器会根据搜索表单内容构建Sql where过滤语句
        // 当然，你也可以在Model中重新定义where语句内容
        $grid->filter($filter);

        // 设置表格
        // 可以自定义行、列、表头的内容和样式等，也可以追加列
        $this->setupTable($grid->table());

        // 渲染模板
        return $content->render();
    }

    protected function setupTable(Table $table)
    {
        /**
         * 自定义字段标题内容
         *
         * @param Th $th 标题对象
         */
        $table->th('name', function (Th $th) {
            // 设置标题颜色
            $th->style('color:green;font-weight:600');
            // 设置属性
            $th->attribute('data-test', 123);

            // 设置标题显示内容
            $th->value('<span>NAME</span>');
        });

        // 字段显示内容自定义：直接设置内容
        // 如果一个字段调用了field自定义处理之后，初始配置的字段渲染方法将不再执行
        $table->field('order_num', '*****');

        /**
         * 字段显示内容自定义：使用匿名函数可以更灵活的定义想要的内容
         * 匿名函数接受2个参数
         *
         * @param mixed $value 原始字段值
         * @param Td $td 表格列字段管理对象（Table > Tr > Th, Td）
         * @param Tr $tr Table > Tr
         */
        $table->field('price', function ($value, Td $td, Tr $tr) {
            // 获取当前行数据
//            $row = $tr->row();

            return $value + 100;
        });

        /**
         * 追加额外的列到最前面
         *
         * @param array $row 当前行数据
         * @param Td $td 追加的列Td对象
         * @param Th $th 追加的列Th对象
         * @paran Tr $tr 追加的列Tr对象
         */
        $table->prepend('序号', function (array $row, Td $td, Th $th, Tr $tr) {
            if (($line = $tr->line()) == 3) {
                // 给第三行添加active样式
                $tr->class('active');
            }

            return $line;
        });

        // 增加额外的行
        $table->append('下班了', '真的嘛？！');

        $table->append('呵呵', function (array $row, Td $td, Th $th, Tr $tr) {
            // 设置标题样式
            $th->style('color:red;font-weight:600');
            $th->class('test-class');
            // 默认隐藏
            $th->hide();

            return '#' . $tr->line();
        });

        $table->append(function (array $row, Td $td, Th $th) {
            $th->value('叫什么好呢？');
            return '演示一下而已~';
        });

        // 添加列到指定位置
        // [column]方法添加的列一定在[prepend]和[append]方法中间
        $table->column(1, '元旦', '放假1天');
        $table->column(5, '王者荣耀', function (array $row, Td $td, Th $th, Tr $tr) {
            return '<b style="">李白</b> ';
        });
        $table->column(6, '王者荣耀', '韩信');
        $table->column(100, '王者荣耀', '大乔');

        // 定义行内容
        $table->tr(function (Tr $tr, $row) {
           $tr->style('color:green');
        });
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
