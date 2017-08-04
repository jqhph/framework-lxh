<?php
/**
 * 模型生成器
 *
 * @author Jqh
 * @date   2017/7/21 17:14
 */

namespace Lxh\Kernel\Builder\Driver;

class Model extends FileGenerator
{
    protected $defaultStub = 'model-record';
    
    public function preview(array $options)
    {
        $this->setOptions($options);
    }

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
                '{beforeAdd}' => $this->makeBeforeAddContent(),
                '{beforeSave}' => $this->makeBeforeSaveContent(),
                '{controllerEnName}' => $this->options('en_name'),
                '{beforeDelete}' => $this->makeBeforeDeleteContent(),
            ]
        );
    }


    protected function makeBeforeAddContent()
    {
        return '';
    }

    protected function makeBeforeSaveContent()
    {
        return '';
    }

    protected function makeBeforeDeleteContent()
    {
        return '';
    }

    /**
     * 设置文件存储文件夹
     *
     * @return string
     */
    protected function getFolder()
    {
        $module = $this->generator->module();
        return "application/$module/Model";
    }


}
