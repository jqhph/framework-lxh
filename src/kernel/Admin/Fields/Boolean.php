<?php

namespace Lxh\Admin\Fields;

use Lxh\Contracts\Support\Renderable;

class Boolean extends Field
{
    protected $options = [
        'color' => 'success'
    ];

    public function render()
    {
        $checked = $this->value ? 'checked="checked"' : '';

        $color = $this->option('color');

        return <<<EOF
<div class="fields"><div class="checkbox checkbox-{$color}"><input type="checkbox" disabled {$checked}><label></label></div></div>
EOF;
    }
}
