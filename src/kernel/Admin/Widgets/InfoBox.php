<?php

namespace Lxh\Admin\Widgets;

use Lxh\Contracts\Support\Renderable;

class InfoBox extends Widget implements Renderable
{
    /**
     * @var string
     */
    protected $view = 'admin::widget.info-box';

    /**
     * @var array
     */
    protected $data = [];

    /**
     * InfoBox constructor.
     *
     * @param string $name
     * @param string $icon
     * @param string $color
     * @param string $link
     * @param string $info
     * @param array $options
     */
    public function __construct($name, $icon, $color, $link, $info, array $options = [])
    {
        $this->data = [
            'name' => $name,
            'icon' => $icon,
            'link' => $link,
            'info' => $info,
            'color' => $color,
            'label' => '',
            'badgeValue' => '',
            'actions' => [],
        ];

        $this->data = array_merge($this->data, $options);
    }

    /**
     * @return string
     */
    public function render()
    {
        $variables = array_merge($this->data, ['attributes' => $this->formatAttributes()]);

        return view($this->view, $variables)->render();
    }
}
