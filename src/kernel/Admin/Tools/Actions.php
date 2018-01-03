<?php

namespace Lxh\Admin\Tools;

use Lxh\Admin\Admin;
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

    /**
     * 添加一条分割线
     *
     * @return $this
     */
    public function divider()
    {
        return $this->append('<li role="separator" class="divider"></li>');
    }

    public function render()
    {
        $tools = '';
        foreach ($this->tools as &$tool) {
            if ($tool instanceof Renderable) {
                $tools .= '<li>' . $tool->render() . '</li>';
            } elseif ($tool instanceof \Closure) {
                $tools = '<li>' . $tool($this) . '</li>';
            } else {
                $tools .= '<li>' . $tool . '</li>';
            }
        }

        Admin::script('$(\'.dropdown-toggle\').dropdown()');

        return <<<EOF
<button class="btn btn-default dropdown-toggle waves-effect" data-toggle="dropdown" aria-expanded="false">{$this->label}&nbsp;<span class="caret"></span></button>
<ul class="dropdown-menu" style="margin-top:9px;">$tools</ul>
EOF;

    }
}
