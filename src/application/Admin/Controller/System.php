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
    public function initialize()
    {

    }

    /**
     * 创建报表界面
     */
    public function actionCreateReports()
    {

        return $this->render('reports', [], true);
    }

    /**
     * 创建模块接口
     *
     */
    public function actionCreateModule()
    {
        $code = make('code.generator');

        $fields = $code->make($_REQUEST);

        print_r($fields->all());

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
        $controllers = ['Lxh\Kernel\Controller\Record'];

        // 字段类型
        $fields = [
            [
                'label' => 'char',
                'list' => [
                    ['id' => 'char', 'name' => 'char'],
                    ['id' => 'date-char', 'name' => 'date'],
                    ['id' => 'password-char', 'name' => 'password'],
                ]
            ],
            [
                'label' => 'int',
                'list' => [
                    ['id' => 'int', 'name' => 'int'],
                ]
            ],
            [
                'label' => 'enum',
                'list' => [
                    ['id' => 'enum', 'name' => 'enum'],
                    ['id' => 'fliter-enum', 'name' => 'fliter'],
                    ['id' => 'align-enum', 'name' => 'items'],
                ]
            ],

        ];

        $groups = [
            'primary',
            'advanced',
        ];

        $nav = 'Making modules';

        $this->share('navTitle', $nav);
        $this->share('fields', $fields);
        $this->share('groups', $groups);
        $this->share('moduleOptions', $options);
        $this->share('controllerOptions', $controllers);

        return $this->render('modules', [], true);
    }

    /**
     * 设置界面
     */
    public function actionSetting()
    {
        $languageList = files()->getFileList(language()->getBasePath());

        $list = [];
        foreach ($languageList as & $lang) {
            $list[] = $lang;
        }

        return $this->render('setting', ['languageList' => & $list], true);
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
