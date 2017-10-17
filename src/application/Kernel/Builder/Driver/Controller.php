<?php
/**
 * 控制器生成器
 *
 * @author Jqh
 * @date   2017/7/21 17:13
 */

namespace Lxh\Kernel\Builder\Driver;

use Lxh\Helper\Util;

class Controller extends FileGenerator
{
    protected $defaultStub = 'controller-record';
    
    /**
     * 预览效果
     *
     * @return string
     */
    public function preview(array $options)
    {
        $this->setOptions($options);
    }

    /**
     * 预览代码
     *
     * @return string
     */
    public function previewCode(array $options)
    {
        $this->setOptions($options);
    }

    protected function buildClass($name)
    {
        return strtr(
            parent::buildClass($name),
            [
                '{module}' => $this->generator->module(),
                '{extends}' => $this->options('inheritance'),
                '{updateValidate}' => $this->makeUpdateValidate(),
                '{deleteValidate}' => $this->makeDeleteValidate(),
                '{controllerEnName}' => $this->options('en_name'),
                '{listTableTitles}' => $this->makeListItems(),
                '{maxSize}' => $this->options('limit', 20),
                '{whereContent}' => $this->makePhpWhereContent(),
                '{searchItems}' => $this->makePhpSearchItems(),
                '{detailFields}' => $this->makeDetailItems(),
            ]
        );
    }

    /**
     * 设置文件存储文件夹
     *
     * @return string
     */
    protected function getFolder()
    {
        $module = $this->generator->module();
        return "application/$module/Controller";
    }

    protected function makeUpdateValidate()
    {
        return '';
    }

    protected function makeDeleteValidate()
    {
        return '';
    }

    /**
     * 生成详情页字段视图信息
     *
     * @return string
     */
    protected function makeDetailItems()
    {
        return '[]';
    }

    protected function makePhpWhereContent()
    {
        return '[\'deleted\' => 0]';
    }

    // 生成搜索项配置数组
    protected function makePhpSearchItems()
    {
        $fields = $this->fields();

        $items = [];

        // 循环获取字段列表
        foreach ($fields->nameList() as & $name) {
            // 判断字段是否是搜索项，否则跳过
            $isSearchItem = $fields->name($name)->options('isSearchItem');

            if (! $isSearchItem) continue;

            // 获取字段类型
            $view = $fields->name($name)->options('view');

            $items[0][] = [
                'view' => $this->getSearchView($view),
                'vars' => [
                    'name' => $name,
                ]
            ];
        }

        return Util::arrayToText($items);
    }

    // 获取字段搜索项公共模板
    protected function getSearchView($view)
    {
        
    }

    protected function makeListItems()
    {
        return '[]';
    }

}
