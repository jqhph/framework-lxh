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

    public function initialize()
    {
    }

    protected $maxSize = 20;

    /**
     * 获取list页table标题信息
     *
     * @return array
     */
    protected function makeListItems()
    {
        return [
            'id' => ['priority' => 0,],
            'name' => [],
            'price' => ['view' => 'price/list'],
            'counter_price' => ['view' => 'price/list'],
            'share_price' => ['view' => 'price/list'],
            'level' => [],
            'stock' => [],
            'is_hot' => ['view' => 'bool/list'],
            'is_new' => ['view' => 'bool/list'],
            'calendar' => ['view' => 'bool/list'],
            'order_num' => [],
            'desc' => [],
            'category_id' => ['view' => ''],
            'created_at' => ['view' => 'varchar/date-list'],
            'modified_at' => ['view' => 'varchar/date-list'],
            'created_by' => [],
        ];
    }

    public function actionList(Request $req, Response $resp, array & $params)
    {
        $content = $this->admin()->content();
        $content->header(trans(__CONTROLLER__));
        $content->description(trans(__CONTROLLER__ . ' list'));

        $content->row(function (Row $row) {
            $row->column(12, $this->filter()->render());
        });

        $content->row(function (Row $row) {
            $row->column(12, $this->grid()->render());
        });

        return $content->render();
    }

    protected function filter()
    {
        $filter = new Filter();

        return $filter;
    }

    protected function grid()
    {
        $grid = new Grid([
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
            'desc' => [],
            'category_id' => [],
            'created_at' => ['view' => 'Date'],
            'modified_at' => ['view' => 'Date'],
            'created_by' => ['view' => 'Date'],
        ]);

        return $grid;
    }


    protected function makeSearchItems()
    {
        return [
            [
                ['view' => 'varchar/search', 'vars' => ['name' => 'name']],
                ['view' => 'varchar/date-search', 'vars' => ['name' => 'created_at']],

            ],
            [
                ['view' => 'enum/align-search', 'vars' => ['name' => 'level', 'options' => [1, 2, 3, 4, 5]]],
                ['view' => 'enum/fliter-search', 'vars' => ['name' => 'created_by_id', 'options' => [1, 2, 3, 4]]],
            ],
        ];
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
