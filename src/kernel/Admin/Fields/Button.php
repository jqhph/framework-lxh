<?php

namespace Lxh\Admin\Fields;

use Lxh\Admin\Admin;
use Lxh\Contracts\Support\Renderable;

class Button extends Field
{
    /**
     * @var string
     */
    protected $label;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $icon;

    protected $options = [
        'color' => 'primary',
        'useTab' => true,
        'id' => 'button',
    ];

    public function __construct($label, $url = null)
    {
        $this->label = $label;
        $this->url = $url;
    }

    /**
     * @param $color
     * @return $this|mixed
     */
    public function color($color)
    {
        return $this->option('color', $color);
    }

    /**
     * 添加图标
     */
    public function icon($icon)
    {
        $this->icon = &$icon;
        return $this;
    }

    public function render()
    {
        $this->class("waves-effect btn btn-{$this->option('color')}");
        $this->attribute('onclick', $this->url());
        $this->buildSelectorAttribute();

        $icon = '';
        if ($this->icon) {
            $icon = "&nbsp; <i class='{$this->icon}'></i>";
        }

        return "<button {$this->formatAttributes()}>{$this->label}{$icon}</button>";
    }

    /**
     * 绑定js事件
     *
     * @param $event
     * @param $callback
     */
    public function on($event, $callback)
    {
        Admin::script(<<<EOT
        $('{$this->getElementSelector()}').on('$event', function() {
            $callback
        });
EOT
        );
    }

    /**
     * @param string $url
     * @return $this|string
     */
    public function url($url = null)
    {
        if ($url) {
            $this->url = &$url;
            return $this;
        }

        if ($this->option('useTab') && $this->url && ($name = $this->name())) {
            return "open_tab('{$name}', '{$this->url}', '{$this->label}')";
        }
        return $this->url;
    }
}
