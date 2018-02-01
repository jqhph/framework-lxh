<?php
/**
 *
 * @author Jqh
 * @date   2017-10-16 21:10:55
 */

namespace Lxh\Admin\Controllers;

use Lxh\Admin\Fields\Expand;
use Lxh\Admin\Fields\Helper;
use Lxh\Admin\Fields\Image;
use Lxh\Admin\Http\Controllers\Controller;
use Lxh\Admin\Layout\Content;
use Lxh\Admin\Filter;
use Lxh\Admin\Grid;
use Lxh\Admin\Layout\Row;
use Lxh\Admin\Table\Column;
use Lxh\Admin\Table\Table;
use Lxh\Admin\Table\Td;
use Lxh\Admin\Table\Th;
use Lxh\Admin\Table\Tr;
use Lxh\Admin\Widgets\Form;
use Lxh\Admin\Widgets\Tab;
use Lxh\Http\Request;
use Lxh\Http\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Product extends Controller
{
    /**
     * 网格报表配置
     *
     * @var array
     */
    protected $grid = [
        'id' => ['hide' => 1, 'sortable' => 1],
        'name' => ['sortable' => 1],
        'price' => ['sortable' => 1,],
        'counter_price',
        'share_price' => ['sortable' => 1,],
        'level',
        'stock',
        'is_hot' => ['view' => 'checked'],
        'is_new' => ['view' => 'checked'],
        'calendar' => ['view' => 'checked'],
        'order_num',
        'desc' => ['show' => 0],
        'category_id' => ['show' => 0],
        'created_at' => ['view' => 'Date'],
        'modified_at' => ['view' => 'Date'],
    ];

    /**
     * 开启过滤器
     *
     * @var bool
     */
    protected $filter = true;

    public function initialize()
    {
    }

    /**
     * 自定义过滤器
     *
     * @param Filter $filter
     */
    protected function filter(Filter $filter)
    {
        $filter->useModal();

        $filter->multipleSelect('status')->options(range(1, 10));
        $filter->text('stock')->number();
        $filter->text('name')->minlen(3);
        $filter->select('level')->options([1, 2]);
        $filter->text('price');
        $filter->dateRange('created_at')->between()->toTimestamp();
    }

    /**
     * 自定义网格配置
     *
     * @param Grid $grid
     */
    protected function grid(Grid $grid, Content $content)
    {
        $grid->allowBatchDelete();
    }

    protected function form(Form $form, Content $content)
    {
        $form->switching('stock');

        $form->divide();
        $form->decimal('test');
        $form->url('level');
        $form->currency('price');
    }

    /**
     * 自定义table
     *
     * @param Table $table
     * @throws \Lxh\Exceptions\InvalidArgumentException
     */
    protected function table(Table $table)
    {
//        $table->image('stock')->rendering(function (Image $image) {
//            $image->value('https://img3.doubanio.com/view/photo/s_ratio_poster/public/p2511434383.jpg');
//        });

        $table->helper('stock')->rendering(function (Helper $helper) {
            $helper->content('test');
        });

        /**
         * 使用field方法添加字段
         */
        $table->expand('name')
            ->sortable()
            ->rendering(function (Expand $expand) {
                $table = new \Lxh\Admin\Widgets\Table([], [
                    ['name' => 'PHP version',       'value' => 'PHP/'.PHP_VERSION],
                    ['name' => 'Lxh-framework version',   'value' => 'dev'],
                    ['name' => 'CGI',               'value' => php_sapi_name()],
                    ['name' => 'Uname',             'value' => php_uname()],
                    ['name' => 'Server',            'value' => get_value($_SERVER, 'SERVER_SOFTWARE')],
                ]);

                $expand->content($table->render());
            });

        /**
         * 自定义字段标题内容
         *
         * @param Th $th 标题对象
         */
        $table->text('name')->th(function (Th $th) {
            // 设置标题颜色
            $th->style('color:green;font-weight:600');
            // 设置属性
            $th->attribute('data-test', 123);

            // 设置标题显示内容
            $th->value('<span>NAME</span>');
        });

        // 字段显示内容自定义：直接设置内容
        // 如果一个字段调用了field自定义处理之后，初始配置的字段渲染方法将不再执行
        $table->field('order_num')->display('*****');

        /**
         * 字段显示内容自定义：使用匿名函数可以更灵活的定义想要的内容
         * 匿名函数接受2个参数
         *
         * @param mixed $value 原始字段值
         * @param Td $td 表格列字段管理对象（Table > Tr > Th, Td）
         * @param Tr $tr Table > Tr
         */
        $table->field('price')->display(function ($value, Td $td, Tr $tr) {
            // 获取当前行数据
//            $row = $tr->row();
            $data = [
                1 => 'color:red',
                2 => 'color:blue'
            ];
            $default = 'color:#333';

            $td->style(get_value($data, $value, $default));

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
//           $tr->style('color:green');
        });
    }

}
