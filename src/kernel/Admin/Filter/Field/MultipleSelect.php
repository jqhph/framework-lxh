<?php

namespace Lxh\Admin\Filter\Field;

use Lxh\Admin\Admin;
use Lxh\Admin\Form\Field;
use Lxh\Contracts\Support\Arrayable;
use Lxh\Support\Str;
use Lxh\Admin\Filter\Equal;
use Lxh\Admin\Filter\Gt;
use Lxh\Admin\Filter\Lt;
use Lxh\Admin\Filter\Between;
use Lxh\Admin\Filter\Where;
use Lxh\Admin\Filter\Like;
use Lxh\Admin\Filter\Ilike;

/**
 *
 * @method Equal equal()
 * @method Gt gt()
 * @method Lt lt()
 * @method Ilike ilike()
 * @method Like like()
 * @method Between between()
 * @method Where where(callable $callable)
 */
class MultipleSelect extends Field\MultipleSelect
{
    use Condition;

    protected $view = 'admin::filter.multiple-select';

    protected $width = ['field' => 3];


    protected function variables()
    {
        $name = $this->name();
        if ($value = I($name)) {
            $this->value = $value;
        }
        $this->value = (array) $this->value;

        return parent::variables();
    }
}
