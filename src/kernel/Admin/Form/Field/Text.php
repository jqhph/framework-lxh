<?php

namespace Lxh\Admin\Form\Field;

use Lxh\Admin\Form\Field;

class Text extends Field
{
    use PlainInput;

    /**
     * Render this filed.
     *
     * @return \Lxh\Contracts\View\Factory|\Lxh\View\View
     */
    public function render()
    {
        $this->initPlainInput();

        $this->prepend('<i class="fa fa-pencil"></i>')
            ->defaultAttribute('type', 'text')
            ->defaultAttribute('id', $this->id)
            ->defaultAttribute('name', $this->elementName ?: $this->formatName($this->column))
            ->defaultAttribute('value', old($this->column, $this->value()))
            ->defaultAttribute('class', 'form-control '.$this->getElementClassString())
            ->defaultAttribute('placeholder', $this->getPlaceholder());

        return parent::render()->with([
            'prepend' => $this->prepend,
            'append'  => $this->append,
        ]);
    }
}
