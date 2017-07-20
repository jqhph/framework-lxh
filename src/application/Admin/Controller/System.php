<?php
/**
 * 系统管理控制器
 *
 * Created by PhpStorm.
 * User: Jqh
 * Date: 2017/7/19
 * Time: 20:28
 */

namespace Lxh\Admin\Controller;

use Lxh\Exceptions\Forbidden;
//use Lxh\MVC\Controller;
use Lxh\Http\Request;
use Lxh\Http\Response;
use Lxh\Helper\Valitron\Validator;
use Lxh\Helper\Util;

class System extends Controller
{
    public function __construct()
    {
    }

    /**
     * 创建报表界面
     */
    public function actionCreateReports()
    {

        return fetch_complete_view('Reports');
    }

    /**
     * 创建模块接口
     *
     */
    public function actionCreateModule()
    {
        return $_REQUEST;
    }

    /**
     * 创建模块入口
     */
    public function actionMakeModules()
    {
        $options = [];
        foreach (config('modules') as & $module) {
            $options[] = ['value' => $module];
        }

        $controllers = [['value' => 'Lxh\Admin\Controller\Controller']];


        $fields = [
            [
                'label' => 'Input',
                'list' => [
                    ['id' => 'varchar', 'name' => 'varchar'],
                    ['id' => 'icon', 'name' => 'icon'],
                ]
            ],
            [
                'label' => 'Select box',
                'list' => [
                    ['id' => 'layer-enum', 'name' => 'layer-enum'],
                    ['id' => 'tree-enum', 'name' => 'tree-enum'],
                ]
            ],
            [
                'label' => 'checkbox',
                'list' => [
                    ['id' => 'bool', 'name' => 'bool'],
                ]
            ],
        ];

        $groups = [
            ['value' => 'primary'],
            ['value' => 'advanced'],
        ];

        assign('fields', $fields);
        assign('groups', $groups);
        assign('moduleOptions', $options);
        assign('controllerOptions', $controllers);

        return fetch_complete_view('Modules');
    }
}
