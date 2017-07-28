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
        // TODO: Change the autogenerated stub

        return strtr(
            parent::buildClass($name),
            [
                'module' => $this->options('module'),
                'extends' => $this->options('inheritance'),
                'updateValidate' => $this->normalizeUpdateValidate($this->options()),
                'deleteValidate' => $this->normalizeDeleteValidate(),
                'controllerEnName' => $this->options('en_name'),
                'listTitles' => $this->normalizeClass($this->options()),
                'maxSize' => $this->options('limit', 20),
                'whereContent' => $this->normalizePhpWhere($this->options()),
                'orderByContent' => $this->normalizePhpOrderBy($this->options()),
                'pageContent' => $this->normalizePageContent(),
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
        $module = $this->options('module');
        return "application/$module/Controller";
    }

    protected function normalizeUpdateValidate(array $opts)
    {
    }

    protected function normalizeDeleteValidate()
    {

    }

    /**
     * 获取列表字段title
     *
     * @return string
     */
    protected function normalizeListTitles(array $opts)
    {
        return '';
    }

    protected function normalizePhpWhere(array $opts)
    {
        return '';
    }

    protected function normalizePhpOrderBy(array $opts)
    {
        return '';
    }

    protected function normalizePageContent()
    {
        return '';
    }

}
