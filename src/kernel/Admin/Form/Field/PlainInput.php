<?php

namespace Lxh\Admin\Form\Field;

use Lxh\Admin\Admin;

trait PlainInput
{
    protected function initPlainInput()
    {
        if (empty($this->view)) {
            $this->view = 'admin::form.input';
        }
    }

    /**
     * 设置默认属性
     *
     * @param $attribute
     * @param $value
     * @return $this
     */
    protected function defaultAttribute($attribute, $value)
    {
        if (!isset($this->attributes[$attribute])) {
            $this->attribute($attribute, $value);
        }

        return $this;
    }

    /**
     * 设置为必填
     *
     * @return $this
     */
    public function required()
    {
        $this->attributes['required'] = 'required';
        return $this;
    }

    /**
     * 设置表单类型
     *
     * @param string $type
     * @return static
     */
    public function type($type = 'text')
    {
        return $this->attribute('type', $type);
    }

    /**
     * 只允许输入邮箱号
     *
     * @param string $type
     * @return static
     */
    public function email()
    {
        return $this->attribute('type', 'email');
    }

    /**
     * 只允许输入数字
     *
     * @return $this
     */
    public function number()
    {
        return $this->attribute('type', 'number');
    }

    /**
     * 密码
     *
     * @return $this
     */
    public function password()
    {
        return $this->attribute('type', 'password');
    }

    /**
     * 日期
     *
     * @return $this
     */
    public function date()
    {
        return $this->attribute('type', 'date');
    }

    /**
     * 时间
     *
     * @return $this
     */
    public function time()
    {
        return $this->attribute('type', 'time');
    }

    /**
     * 日期时间
     *
     * @return $this
     */
    public function datetimeLocal()
    {
        return $this->attribute('type', 'datetime-local');
    }

    /**
     * 月
     *
     * @return $this
     */
    public function month()
    {
        return $this->attribute('type', 'month');
    }

    /**
     * 周
     *
     * @return $this
     */
    public function week()
    {
        return $this->attribute('type', 'week');
    }

    /**
     * 最小值
     *
     * @return $this
     */
    public function min($min)
    {
        return $this->attribute('min', $min);
    }

    /**
     * 最小值
     *
     * @return $this
     */
    public function max($max)
    {
        return $this->attribute('max', $max);
    }

    /**
     * @param $min
     * @return $this
     */
    public function minlen($min)
    {
        return $this->attribute('minlength', $min);
    }

    /**
     * @param $min
     * @return $this
     */
    public function maxlen($max)
    {
        return $this->attribute('maxlength', $max);
    }

    protected function attachOptionsScript()
    {
        $class = $this->getElementClassSelector();
        // 点击下拉菜单选项并把选项的值赋给text输入框
        Admin::script(<<<EOF
$('{$class}').next().find('li').click(function (e) {\$('{$class}').val($(this).text())});
EOF
        );
    }
}
