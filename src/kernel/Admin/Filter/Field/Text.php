<?php

namespace Lxh\Admin\Filter\Field;

use Lxh\Admin\Form\Field;
use Lxh\Admin\Filter\Equal;
use Lxh\Admin\Filter\Gt;
use Lxh\Admin\Filter\Lt;
use Lxh\Admin\Filter\Between;
use Lxh\Admin\Filter\Where;
use Lxh\Admin\Filter\Like;
use Lxh\Admin\Filter\Ilike;

/**
 * Class DateRange.
 *
 * @method Equal equal()
 * @method Gt gt()
 * @method Lt lt()
 * @method Ilike ilike()
 * @method Like like()
 * @method Between between()
 * @method Where where(callable $callable)
 */
class Text extends Field
{
    use Condition;

    protected $view = 'admin::filter.text';

    protected $width = [
        'field' => 2
    ];

    protected $append;

    protected function variables()
    {
        $name = $this->name();
        if ($value = I($name)) {
            $this->value = $value;
        }

        $this->defaultAttribute('type', 'text')
            ->defaultAttribute('id', $this->id)
            ->defaultAttribute('name', $name)
            ->defaultAttribute('value', $this->value())
            ->defaultAttribute('class', 'form-control '.$this->getElementClassString())
            ->defaultAttribute('placeholder', $this->getPlaceholder());

        return array_merge(parent::variables(), [
            'filterInput' => $this->getInputHandler()->render()
        ]);
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
