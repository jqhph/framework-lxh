<?php

namespace Lxh\Admin\Widgets;

use Lxh\Admin\Admin;
use Lxh\Contracts\Support\Renderable;

class WaterFall extends Widget implements Renderable
{
    /**
     * @var string
     */
    protected $view = 'admin::widget.water-fall';

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);

        Admin::js('@lxh/js/jquery.wookmark.min');
    }

    protected function vars()
    {
        return [

        ];
    }

    /**
     * @return string
     */
    public function render()
    {
        return view($this->view, $this->vars())->render();
    }
}
