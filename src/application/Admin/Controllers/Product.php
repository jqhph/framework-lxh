<?php
/**
 *
 * @author Jqh
 * @date   2017-10-16 21:10:55
 */

namespace Lxh\Admin\Controllers;

use Lxh\Admin\Admin;
use Lxh\Admin\Data\Items;
use Lxh\Admin\Fields\Button;
use Lxh\Admin\Fields\Editable;
use Lxh\Admin\Fields\Expand;
use Lxh\Admin\Fields\Popover;
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
use Lxh\Admin\Widgets\Alert;
use Lxh\Admin\Widgets\Card;
use Lxh\Admin\Widgets\Code;
use Lxh\Admin\Widgets\Collapse;
use Lxh\Admin\Widgets\Form;
use Lxh\Admin\Widgets\Popup;
use Lxh\Admin\Widgets\Tab;
use Lxh\Admin\Layout;

class Product extends Controller
{
    /**
     * 开启过滤器
     *
     * @var bool
     */
    protected $filter = 'modal';

    /**
     * 使用回收站功能
     *
     * @var bool
     */
    protected $trash = true;

    /**
     * 网格列表宽度
     *
     * @var int
     */
    protected $gridWidth = 12;

    /**
     * 创建页面表单容器宽度
     *
     * @var int
     */
    protected $createFormWidth = 8;

    /**
     * 编辑页面表单容器宽度
     *
     * @var int
     */
    protected $detailFormWidth = 8;

    protected function form(Form $form)
    {
        $form->slider('slider');

        $form->radio('radio')->options(['value1', 'value2', 'value3'])->default('value2');
        $form->checkbox('checkbox')->options(['value1', 'value2', 'value3']);

        $form->divide();

        $form->textarea('textarea');

        $form->select('select')->options([1, 2, 3])->default(3);
        $form->multipleSelect('multiple-select')->options([1, 2, 3]);

        $form->color('color');
        $form->ip('ip');
        $form->mobile('mobile');
        $form->switch('swich');
        $form->icon('icon');
        // 自定义内容
        $form->html('html')->content('HELLO WORLD!')->help('自定义内容');

        $form->divide();

        $form->editor('editor');
    }

    protected function afterFormColumnResolved(Row $row)
    {
        $column = $row->column(4);

        $column->row(function (Row $row) {
            // 再创建一个子表单
            $form = $this->form->create();

            $form->file('files')->allowFileExtensions(['png', 'jpeg']);
            $form->image('image');
            
            $form->multipleFile('multiple-file')->help('多文件上传必须使用异步上传，需要自定义上传接口和删除接口');

            $row->column(12, new Card('文件上传', $form));
        });

        $column->row(function (Row $row) {
            // 创建子表单
            // 把表单拆分成多块布局
            $form = $this->form->create();

            $form->date('date');
            $form->datetime('datetime');
            $form->month('month');
            $form->time('time');
            $form->year('year');

            $form->dateRange('date-range', 2018, 2019);
            $form->dateTimeRange('datetime-range');
            $form->timeRange('time-range');

            $row->column(12, new Card('时间日期', $form));
        });

        // 再创建一个子表单
        $form = $this->form->create();

        $form->decimal('decimal');
        $form->url('url');
        $form->currency('currency');

        $form->map('map', 39.916527, 116.397128);

        $column->row(new Card('地图', $form));
    }

    protected function afterFormRowResolved(Content $content, Card $card)
    {
        $preview = new Button('代码预览');

        $preview->on('click', '
            layer.open({
              type: 2,
              title: \'代码预览\',
              shadeClose: true,
              shade: false,
              area: [\'70%\', \'700px\'],
              content: \'/admin/product/action/form-code-preview\'
            }); 
            return false;
        ');

        $card->rightTools()->prepend($preview);
    }

    public function actionFormCodePreview()
    {
        $content = $this->content();

        $content->row(new Code(__FILE__, 52, 158));

        return $content->render();
    }

    public function actionTest()
    {
        $table = new \Lxh\Admin\Widgets\Table([], [
            ['name' => 'PHP version',       'value' => 'PHP/'.PHP_VERSION],
            ['name' => 'Lxh-framework version',   'value' => 'dev'],
            ['name' => 'CGI',               'value' => php_sapi_name()],
            ['name' => 'Uname',             'value' => php_uname()],
            ['name' => 'Server',            'value' => get_value($_SERVER, 'SERVER_SOFTWARE')],
        ]);

        return $table->render();
    }

    /**
     * 定义过滤器字段
     *
     * @param Filter $filter
     */
    protected function filter(Filter $filter)
    {
        $filter->multipleSelect('status')->options(range(1, 10));
        $filter->text('stock')->number();
        $filter->text('name')->minlen(3);
        $filter->select('level')->options([1, 2]);
        $filter->text('price');
        $filter->dateRange('created_at')->between()->time();
    }

    protected function beforeGridRowResolved(Content $content)
    {
        $content->row(
            new Alert('这只是简单的示例代码')
        );
    }

    /**
     * 定义网格配置
     *
     * @param Grid $grid
     */
    protected function grid(Grid $grid)
    {
        $grid->allowBatchDelete();

        $preview = new Button('代码预览');

        $preview->on('click', '
            layer.open({
              type: 2,
              title: \'代码预览\',
              shadeClose: true,
              shade: false,
              area: [\'70%\', \'700px\'],
              content: \'/admin/product/action/grid-code-preview\'
            }); 
            return false;
        ');

        $grid->tools()->prepend($preview);
    }

    public function actionGridCodePreview()
    {
        $content = $this->content();

        $content->row(new Code(__FILE__, 1, 400));

        return $content->render();
    }

    /**
     * 定义table字段
     *
     * @param Table $table
     * @throws \Lxh\Exceptions\InvalidArgumentException
     */
    protected function table(Table $table)
    {
        $table->code('id')->hide()->sortable();

        $table->editable('name', function (Editable $editable) {
            switch ($editable->line()) {
                case 1:
                    $editable->datetime();
                    break;
                case 2:
                    $editable->url();
                    break;
                case 3:
                    $editable->number();
                    break;
                case 4:
                    $editable->email();
                    break;
                case 5:
                    $editable->select([1, 2, 3]);
            }

        })->th(function (Th $th) {
            // 设置标题颜色
            $th->style('color:green;font-weight:600');
            // 设置属性
            $th->attribute('data-test', 123);

            // 设置标题显示内容
            $th->value('<span>NAME</span>');
        });

        $table->expand('price', function (Expand $expand) {
            $expand->ajax('/admin/product/action/test');
        })
            ->sortable();

        $table->switch('is_hot');
        $table->checked('is_new');

        // 字段显示内容自定义：直接设置内容
        // 如果一个字段调用了field自定义处理之后，初始配置的字段渲染方法将不再执行
        $table->column('order_num')->display('*****');

        /**
         * 字段显示内容自定义：使用匿名函数可以更灵活的定义想要的内容
         * 匿名函数接受2个参数
         *
         * @param mixed $value 原始字段值
         * @param Td $td 表格列字段管理对象（Table > Tr > Th, Td）
         * @param Tr $tr Table > Tr
         */
        $table->column('stock')->display(function ($value, Items $items) {
            return $value + 100;
        });

        $table->date('created_at');

        /**
         * 追加额外的列到最前面
         *
         * @param Items $items 当前行数据
         * @param Td $td 追加的列Td对象
         * @param Th $th 追加的列Th对象
         * @paran Tr $tr 追加的列Tr对象
         */
        $table->prepend('序号', function (Items $items) {
            return $items->offset() + 1;
        });

        // 增加额外的行
        $table->append('下班了', '真的嘛？！');

        $table->append('呵呵', function (Items $items, Td $td, Th $th, Tr $tr) {
            // 设置标题样式
            $th->style('color:red;font-weight:600');
            $th->class('test-class');
            // 默认隐藏
            $th->hide();

            return '#' . $tr->line();
        });

        // 定义行内容
        $table->tr(function (Tr $tr) {
//           $tr->style('color:green');
        });
    }

}
