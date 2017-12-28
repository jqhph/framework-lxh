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

    public function __construct(Field $field = null)
    {
        $this->field = $field;
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
     * 构建where条件数组
     *
     * @return array
     */
    public function buildConditions(array $fields)
    {
        $conditions = [];
        foreach ($fields as &$field) {
            if (($value = $this->condition($field)) === null) {
                continue;
            }
            $conditions[$field] = $value;
        }

        return $conditions;
    }

    /**
     * 返回null则跳过
     *
     * @param $field
     * @return mixed
     */
    abstract protected function condition($field);
}
