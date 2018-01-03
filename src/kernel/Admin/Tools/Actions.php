<?php

namespace Lxh\Admin\Tools;

use Lxh\Contracts\Support\Renderable;

class Actions extends Tools
{
    /**
     * @var string
     */
    protected $label = 'Actions';

    public function __construct($label = null)
    {
        if ($label) $this->label = $label;

        $this->label = trans($this->label);
    }

    public function render()
    {
        $tools = parent::render();

        return <<<EOF
<button class="btn btn-default dropdown-toggle waves-effect" data-toggle="dropdown" aria-expanded="false">{$this->label}&nbsp;<span class="caret"></span></button>
<ul class="dropdown-menu">
                                                    <li><a href="#">Dropdown link 1</a></li>
                                                    <li><a href="#">Dropdown link 2</a></li>
                                                    <li><a href="#">Dropdown link 3</a></li>
                                                </ul>
EOF;

    }
}
