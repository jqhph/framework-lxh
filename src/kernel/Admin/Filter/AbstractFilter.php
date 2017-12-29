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
     * 已处理好的查询条件数组
     *
     * @var array
     */
    protected static $conditionsValue = [];

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
    protected $conditionHandler = null;

    protected $fieldFormatHandler = null;

    public function __construct(Field $field = null)
    {
        $this->field = $field;
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

    public function render()
    {
        return "<input type='hidden' name='{$this->name}[]' value='{$this->value()}' />";
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

    public function __toString()
    {
        return $this->render();
    }

    /**
     * 获取查询条件数据
     *
     * @return mixed
     */
    protected function normalizeInput()
    {
        return I($this->name);
    }

    /**
     * 构建where条件数组
     *
     * @return static
     */
    public function build(array $fields = [])
    {
        $fields = $fields ?: $this->normalizeInput();

        foreach ((array) $fields as &$field) {
            if (isset(static::$conditionsValue[$field])) {
                // 已经构建过，不再重新构建
                continue;
            }

            $key = $this->formatFieldName($field);

            if ($this->conditionHandler) {
                // 自定义处理器处理
                $value =  call_user_func($this->conditionHandler, $fields, $this);

                if ($value === null) continue;

                $conditions[$key] = $value;
                continue;
            }

            // 使用默认构建查询条件数组方法
            if (($value = $this->buildCondition($field)) === null) {
                continue;
            }
            static::$conditionsValue[$key] = $value;
        }

        return $this;
    }

    /**
     * 获取已处理好的查询条件数组
     *
     * @return array
     */
    public static function getConditionsValue()
    {
        return static::$conditionsValue;
    }

    /**
     * 设置格式化查询字段处理器
     *
     * @param callable $handler
     * @return static
     */
    public function formatField(callable $handler)
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
        if ($this->fieldFormatHandler) {
            return call_user_func($this->fieldFormatHandler, $field);
        }

        return $field;
    }

    /**
     * 返回null则跳过
     *
     * @param $field
     * @return mixed
     */
    abstract protected function buildCondition($field);
}
