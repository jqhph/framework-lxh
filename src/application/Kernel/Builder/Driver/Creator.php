<?php
/**
 * CREATOR
 *
 * @author Jqh
 * @date   2017/7/21 17:06
 */

namespace Lxh\Kernel\Builder\Driver;

use Lxh\Kernel\Builder\CodeGenerator;

abstract class Creator
{
    /**
     * @var CodeGenerator
     */
    protected $generator;

    public function __construct(CodeGenerator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * 生成代码
     *
     * @return bool
     */
    abstract public function make(array $options);

    /**
     * 预览效果
     *
     * @return bool | string
     */
    abstract public function preview(array $options);

    /**
     * 预览代码
     *
     * @return bool | string
     */
    abstract public function previewCode(array $options);

    /**
     * 回滚操作
     *
     * @return void
     */
    public function rollback()
    {

    }

}
