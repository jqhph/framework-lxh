<?php

namespace Lxh\Admin\Filter\Field;

use Lxh\Admin\Admin;
use Lxh\Admin\Filter\AbstractFilter;
use Lxh\Contracts\Support\Arrayable;
use Lxh\Admin\Form\Field\Select as FormSelect;
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
class Select extends FormSelect
{
    use Condition;

    protected $view = 'admin::filter.select';

    protected $width = ['field' => 2];

    /**
     * 是否允许清除单选框
     *
     * @var string
     */
    protected $clear = 'true';

    protected function variables()
    {
        $name = $this->name();
        if ($value = I($name)) {
            $this->value = $value;
        }

        return array_merge(parent::variables(), [
            'filterInput' => $this->getInputHandler()->render()
        ]);

    }
}
