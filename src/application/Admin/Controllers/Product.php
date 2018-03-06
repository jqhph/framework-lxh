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
use Lxh\Admin\Widgets\Box;
use Lxh\Admin\Widgets\Card;
use Lxh\Admin\Widgets\Code;
use Lxh\Admin\Widgets\Form;
use Lxh\Admin\Widgets\Markdown;
use Lxh\Admin\Widgets\Tab;
use Lxh\Exceptions\InvalidArgumentException;
use Lxh\Helper\Util;
use Lxh\Http\Request;
use Lxh\Http\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
     * @var int
     */
    protected $gridWidth = 12;

    protected $createFormWidth = 8;
    protected $detailFormWidth = 8;

    public function initialize()
    {
    }

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

    public function actionUploadImage(array $params)
    {
        // 上传路径
        $directory = $this->getImageUploadDirectory();

        $allowFileExtensions = [];

        return $this->upload($directory, $allowFileExtensions);
    }

    protected function upload($directory, array $allowFileExtensions = [])
    {
        $uploadFiles = [];

        $errors = [];
        $files = [];

        foreach ($_FILES as $k => &$file) {
            $files[] = $k;

            $uploadFiles[] = $uploadFile = new UploadedFile(
                $file['tmp_name'], $file['name'], $file['type'], $file['size'], $file['error']
            );

            $ext = $uploadFile->guessClientExtension();

            if ($allowFileExtensions && !in_array($ext, $allowFileExtensions)) {
                $errors[] = sprintf(
                    trans('Invalid extension for file "%s". Only "%s" files are supported.', 'tip'),
                    $file['name'],
                    implode(', ', $allowFileExtensions)
                );

                continue;
            }

            try {
                $targets[$k] = $uploadFile->move(
                    $directory,
                    $this->generateUniqueFileName($uploadFile, $ext)
                );
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }

        }

        return ['error' => implode('<br>', $errors), 'filenames' => &$files];
    }

    /**
     * Default directory for file to upload.
     *
     * @return mixed
     */
    public function getImageUploadDirectory()
    {
        return config('admin.upload.directory.image', __ROOT__ . 'uploads/images') . '/' . __CONTROLLER__;
    }

    /**
     * Default directory for file to upload.
     *
     * @return mixed
     */
    public function getFileUploadDirectory()
    {
        return config('admin.upload.directory.file', __ROOT__ . 'uploads/files') . '/' . __CONTROLLER__;
    }

    /**
     * Generate a unique name for uploaded file.
     *
     * @param UploadedFile $file
     *
     * @return string
     */
    protected function generateUniqueFileName(UploadedFile $file, $ext = null)
    {
        return md5(uniqid()).'.'.($ext ?: $file->guessExtension());
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

        $content->row(new Code(__FILE__, 52, 144));

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

        $content->row(new Code(__FILE__, 157, 400));

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
        $table->field('order_num')->display('*****');

        /**
         * 字段显示内容自定义：使用匿名函数可以更灵活的定义想要的内容
         * 匿名函数接受2个参数
         *
         * @param mixed $value 原始字段值
         * @param Td $td 表格列字段管理对象（Table > Tr > Th, Td）
         * @param Tr $tr Table > Tr
         */
        $table->field('stock')->display(function ($value, Td $td, Tr $tr) {
            // 获取当前行数据
//            $row = $tr->items()->all();
            $data = [
                1 => 'color:red',
                2 => 'color:blue'
            ];
            $default = 'color:#333';

            $td->style(get_value($data, $value, $default));

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
