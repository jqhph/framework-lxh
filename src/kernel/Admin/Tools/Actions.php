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

    /**
     * @var \Closure
     */
    protected $rendering;

    public function __construct()
    {
        $this->label = trans($this->label);
    }

    /**
     * 设置按钮名称
     *
     * @param $label
     * @return $this
     */
    public function label($label)
    {
        $this->label = $label;
        return $this;
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
<div class="btn-group">
<button class="btn btn-default dropdown-toggle waves-effect" data-toggle="dropdown" aria-expanded="false">{$this->label}&nbsp;<span class="caret"></span></button>
<ul class="dropdown-menu">$tools</ul>
</div>
EOF;

    }
}
