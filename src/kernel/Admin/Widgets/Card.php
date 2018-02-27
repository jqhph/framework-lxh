<?php

namespace Lxh\Admin\Widgets;

class Card extends Box
{
    /**
     * @var string
     */
    protected $view = 'admin::widget.card';


    public function __construct($title = '', $content = '')
    {
        parent::__construct($title, $content);

        $this->attribute('class', 'card');
    }
}
