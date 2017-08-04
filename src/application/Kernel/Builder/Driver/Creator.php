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
     * @var array
     */
    private $options;

    private $contents;

    /**
     * @var CodeGenerator
     */
    protected $generator;

    public function __construct(CodeGenerator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * @return Fields
     */
    public function fields()
    {
        return $this->generator->fields();
    }

    /**
     * 设置或获取生成内容
     *
     * @param  mixed $data
     * @return mixed
     */
    public function content($data = null)
    {
        if ($data) {
            $this->contents = $data;
            return;
        }
        return $this->contents;
    }

    /**
     * 生成代码
     *
     * @return mixed
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

    /**
     * 获取配置文件
     *
     * @param  string $name
     * @param  mixed  $default
     * @return mixed
     */
    protected function options($name, $default = null)
    {
        if (empty($name)) {
            return $this->options;
        }
        return isset($this->options[$name]) ? $this->options[$name] : $default;
    }

    protected function setOptions(array $opts)
    {
        $this->options = $opts;
    }

}
