<?php

namespace Lxh\Admin\Filter\Field;

use Lxh\Admin\Admin;
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
    use Field\PlainInput, Condition;

    /**
     * @var string
     */
    protected $view = 'admin::filter.text';

    /**
     * @var array
     */
    protected $width = [
        'field' => 2
    ];

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

        if ($this->options) {
            // 下拉点击菜单
            $this->attribute('data-toggle', 'dropdown');

            $this->attachOptionsScript();
        }

        return parent::variables();
    }


}
