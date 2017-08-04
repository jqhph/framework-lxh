<?php
/**
 * 控制器生成器
 *
 * @author Jqh
 * @date   2017/7/21 17:13
 */

namespace Lxh\Kernel\Builder\Driver;

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
                '{listTableTitles}' => $this->makeListTableTitles(),
                '{maxSize}' => $this->options('limit', 20),
                '{whereContent}' => $this->makePhpWhereContent(),
                '{searchItems}' => $this->makePhpSearchItems(),
                '{detailFields}' => $this->makeDetailFields(),
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
    protected function makeDetailFields()
    {
        return '[]';
    }

    protected function makePhpWhereContent()
    {
        return '[\'deleted\' => 0]';
    }

    protected function makePhpSearchItems()
    {
        return '[]';
    }

    protected function makeListTableTitles()
    {
        return '[]';
    }

}
