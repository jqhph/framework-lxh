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

    /**
     * Grid item width
     *
     * @var int
     */
    protected $width = 250;

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);

        Admin::js('@lxh/js/jquery.wookmark.min');
    }

    /**
     * @param $width
     * @return $this
     */
    public function width($width)
    {
        $this->width = $width;
        return $this;
    }

    public function height()
    {
        
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
