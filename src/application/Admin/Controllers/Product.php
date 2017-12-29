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
        'name' => ['sortable' => 1, 'desc' => 0],
        'price' => ['sortable' => 1,],
        'counter_price' => [],
        'share_price' => ['sortable' => 1,],
        'level' => [],
        'stock' => [],
        'is_hot' => ['view' => 'Boolean'],
        'is_new' => ['view' => 'Boolean'],
        'calendar' => ['view' => 'Boolean'],
        'order_num' => [],
        'desc' => ['show' => 0],
        'category_id' => ['show' => 0],
        'created_at' => ['view' => 'Date'],
        'modified_at' => ['view' => 'Date'],
        'created_by' => ['view' => 'Date'],
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
            $filter->dateRange('created_at');
        });

        // 构建网格报表
        $content->grid($this->grid)->filter($filter);

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
