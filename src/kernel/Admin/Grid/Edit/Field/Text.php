<?php

namespace Lxh\Admin\Grid\Edit\Field;

use Lxh\Admin\Form\Field\PlainInput;

class Text extends Field
{
    use PlainInput;

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
