<?php

namespace Lxh\Admin\Form\Field;

use Lxh\Admin\Form\Field;

class Html extends Field
{
    /**
     * Htmlable.
     *
     * @var string|\Closure
     */
    protected $html = '';

    /**
     * @param $html
     * @return $this
     */
    public function content($html)
    {
        $this->html = &$html;

        return $this;
    }

    /**
     * Render html field.
     *
     * @return string
     */
    public function render()
    {
        if ($this->html instanceof \Closure) {
            $this->html = call_user_func($this->html, $this->form);
        }

        $prepend = $this->prepend ? $this->prepend . '&nbsp ' : '';

        return <<<EOT
<div class="form-group line">
    <div class="col-sm-{$this->width['field']}"><div class="text">{$prepend}{$this->label()}</div>{$this->html}</div>
</div>
EOT;
    }
}
