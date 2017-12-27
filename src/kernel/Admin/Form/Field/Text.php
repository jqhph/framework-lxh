<?php

namespace Lxh\Admin\Form\Field;

use Lxh\Admin\Form\Field;

class Text extends Field
{
    use PlainInput;

    protected function variables()
    {
        $this->prepend('<i class="fa fa-pencil"></i>')
            ->defaultAttribute('type', 'text')
            ->defaultAttribute('id', $this->id)
            ->defaultAttribute('name', $this->elementName ?: $this->formatName($this->column))
//            ->defaultAttribute('value', $this->form->value())
            ->defaultAttribute('class', 'form-control '.$this->getElementClassString())
            ->defaultAttribute('placeholder', $this->getPlaceholder());

        $this->variables['prepend'] = &$this->prepend;
        $this->variables['append'] = &$this->append;

        return parent::variables();
    }

    public function render()
    {
        $this->initPlainInput();
        return parent::render(); // TODO: Change the autogenerated stub
    }
}
