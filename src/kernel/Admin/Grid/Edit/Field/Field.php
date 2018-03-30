<?php

namespace Lxh\Admin\Grid\Edit\Field;

use Lxh\Admin\Data\Items;
use Lxh\Admin\Grid\Edit\Form;

class Field extends \Lxh\Admin\Form\Field
{
    /**
     * @var Form
     */
    protected $form;

    /**
     * @var Items $items
     */
    protected $items;

    public function setEditForm(Form $form)
    {
        $this->form  = $form;
        $this->items = $form->items();

        $this->setupValue();

        return $this;
    }

    protected function setupValue()
    {
        if ($this->value === false) return $this;
        // Field value is already setted.
        if (is_array($this->column)) {
            foreach ($this->column as $key => &$column) {
                $this->value[$key] = $this->items->get($column);
            }

            return $this;
        }

        $this->value = $this->items->get($this->column);
    }

    public function formatRules()
    {
        if (! $this->rules) {
            return '';
        }

        $rule = json_encode([
            'name' => $this->column, 'rules' => &$this->rules
        ]);

        return "<script>window['formrules'.$this->form->getPrimaryKey()].push({$rule})</script>";
    }
}
