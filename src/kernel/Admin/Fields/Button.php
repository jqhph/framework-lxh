<?php

namespace Lxh\Admin\Fields;

use Lxh\Contracts\Support\Renderable;

class Button extends Field
{
    protected $label;

    protected $url;

    protected $options = [
        'color' => 'primary',
        'useTab' => true,
        'id' => 'button',
    ];

    public function __construct($label, $url, array $options)
    {
        $this->label = $label;
        $this->url = $url;
        parent::__construct('', '', $options);
    }

    public function render()
    {
        return "<a href=\"{$this->url()}\" data-action=\"create-row\" class=\"btn btn-{$this->option('color')}\">{$this->label}</a>";
    }

    protected function url()
    {
        if ($this->option('useTab')) {
            return "javascript:open_tab('{$this->option('id')}', '{$this->url}', '{$this->label}')";
        }
        return $this->url;
    }
}