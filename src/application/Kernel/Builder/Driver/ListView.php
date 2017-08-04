<?php
/**
 * list模板生成
 *
 * @author Jqh
 * @date   2017/8/4 17:01
 */

namespace Lxh\Kernel\Builder\Driver;

class ListView extends FileGenerator
{
    protected $defaultStub = 'view-list';

    public function make(array $options)
    {

    }

    public function preview(array $options)
    {

    }

    public function previewCode(array $options)
    {

    }

    public function getPath()
    {
        $module = $this->generator->module();

        return $this->getBasePath() . "application/$module/View/List.php";
    }

}
