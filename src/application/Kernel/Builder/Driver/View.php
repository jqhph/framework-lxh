<?php
/**
 * 模型生成器
 *
 * @author Jqh
 * @date   2017/7/21 17:14
 */

namespace Lxh\Kernel\Builder\Driver;

use Lxh\Kernel\Builder\CodeGenerator;

class View extends Creator
{
    protected $listView;

    protected $detailView;

    protected $searchView;

    public function __construct(CodeGenerator $generator)
    {
        parent::__construct($generator);
    }

    public function make(array $options)
    {

    }

    public function preview(array $options)
    {

    }

    public function previewCode(array $options)
    {

    }

}
