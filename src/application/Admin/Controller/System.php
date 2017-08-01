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

        // 可选的控制器父类
        $controllers = [['value' => 'Lxh\Kernel\Controller\Record']];

        // 字段类型
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

        $nav = 'Making modules';

//        assign('navTitle', $nav);
        assign('fields', $fields);
        assign('groups', $groups);
        assign('moduleOptions', $options);
        assign('controllerOptions', $controllers);

        return fetch_complete_view('Modules');
    }

    /**
     * 设置界面
     */
    public function actionSetting()
    {
        $languageList = file_manager()->getFileList(language()->getBasePath());

        $list = [];
        foreach ($languageList as & $lang) {
            $list[]['value'] = $lang;
        }

        return fetch_complete_view('Setting', ['languageList' => & $list]);
    }

    // 清除客户端所有缓存接口
    public function actionClearAllClientCache()
    {
        if (! user()->isAdmin()) {
            return $this->failed('Forbidden');
        }

        if (make('front.client')->clearCache()) {
            return $this->success();
        }

        return $this->failed();
    }

    // 清除客户端js缓存接口
    public function actionClearClientCache()
    {
        if (! user()->isAdmin()) {
            return $this->failed('Forbidden');
        }

        if (make('front.client')->updateCache()) {
            return $this->success();
        }

        return $this->failed();
    }
}
