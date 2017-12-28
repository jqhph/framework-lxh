<?php

namespace Lxh\Admin\Filter\Field;

use Lxh\Admin\Form\Field;

class Text extends Field
{
    protected $view = 'admin::filter.text';

    protected $width = [
        'field' => 2
    ];

    protected $append;

    protected function variables()
    {
        $name = $this->elementName ?: $this->formatName($this->column);
        if ($value = I($name)) {
            $this->value = $value;
        }

        $this->defaultAttribute('type', 'text')
            ->defaultAttribute('id', $this->id)
            ->defaultAttribute('name', $this->elementName ?: $this->formatName($this->column))
            ->defaultAttribute('value', $this->value())
            ->defaultAttribute('class', 'form-control '.$this->getElementClassString())
            ->defaultAttribute('placeholder', $this->getPlaceholder());

        return parent::variables();
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

    public function number()
    {
        return $this->attribute('type', 'number');
    }

    public function password()
    {
        return $this->attribute('type', 'password');
    }

    protected function defaultAttribute($attribute, $value)
    {
        if (!isset($this->attributes[$attribute])) {
            $this->attribute($attribute, $value);
        }
        return $this;
    }

}
