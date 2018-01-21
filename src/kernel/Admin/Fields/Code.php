<?php

namespace Lxh\Admin\Fields;

use Lxh\Contracts\Support\Renderable;

class Code extends Field
{
    protected $options = [
        'color' => ''
    ];

    /**
     * @return $this
     */
    public function primary()
    {
        $this->options['color'] = 'primary';
        return $this;
    }

    public function render()
    {
        if (is_array($this->value)) {
            $this->value = json_encode($this->value);
        }

        $color = $this->option('color');

        return $this->value ? "<code class='$color'>{$this->value}</code>" : '';
    }
}
