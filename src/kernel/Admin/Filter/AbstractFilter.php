<?php

namespace Lxh\Admin\Filter;

use Lxh\Admin\Filter;
use Lxh\Admin\Filter\Field\Select;
use Lxh\Admin\Filter\Field\Text;
use Lxh\Admin\Form\Field;
use Lxh\Template\View;

abstract class AbstractFilter
{
    /**
     * @var Field
     */
    protected $field;

    /**
     * @var array
     */
    protected $fields = [];

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var callable
     */
    protected $conditionHandler;

    /**
     * @var mixed
     */
    protected $fieldFormatHandler = null;

    public function __construct(Field $field = null, callable $conditionHandler = null)
    {
        $this->field = $field;

        $this->conditionHandler = $conditionHandler;
    }

    /**
     * @return Field
     */
    public function field()
    {
        return $this->field;
    }

    /**
     * 设置条件构建处理
     *
     * @param callable $callable
     * @return static
     */
    public function condition(callable $callable)
    {
        $this->conditionHandler = $callable;

        return $this;
    }

    /**
     * 过滤器条件表单名
     *
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * 过滤器值
     *
     * @return string
     */
    public function value()
    {
        return $this->field->name();
    }

    /**
     * 构建where条件数组
     *
     * @return mixed
     */
    public function build()
    {
        $field = $this->field->name();

        $key = $this->formatFieldName($field);

        if ($this->conditionHandler) {
            // 自定义处理器处理
            $condition = call_user_func($this->conditionHandler, $field, $this);

            return $condition === null ? false : ($key ? [$key => &$condition] : $condition);
        }

        $input = trim(I($field));

        // 使用默认构建查询条件数组方法
        if ($this->inputInvalid($input) || ($condition = $this->buildCondition($field, $input)) === null) {
            return false;
        }

        return $key ? [$key => &$condition] : $condition;
    }

    /**
     * 判断用户输入的值是否无效
     *
     * @param $field
     * @return bool
     */
    protected function inputInvalid($input)
    {
        return ($input === null || $input === '') ? true : false;
    }

    protected function getFieldValue($field)
    {
        return I($field);
    }

    /**
     * 设置格式化查询字段处理器
     *
     * @param mixed $handler
     * @return static
     */
    public function formatField($handler)
    {
        $this->fieldFormatHandler = $handler;

        return $this;
    }

    /**
     * 格式化查询字段
     *
     * @param string $field
     * @return mixed
     */
    protected function formatFieldName($field)
    {
        if ($this->fieldFormatHandler !== null) {
            return is_callable($this->fieldFormatHandler) ? call_user_func($this->fieldFormatHandler, $field)
                : $this->fieldFormatHandler;
        }

        return $field;
    }

    /**
     * 返回null则跳过
     *
     * @param $field
     * @return mixed
     */
    abstract protected function buildCondition($field, $input);
}
