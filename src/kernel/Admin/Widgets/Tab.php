<?php

namespace Lxh\Admin\Widgets;

use Lxh\Contracts\Support\Renderable;

class Tab extends Widget
{
    /**
     * @var string
     */
    protected $view = 'admin::widget.tab';

    /**
     * @var array
     */
    protected $data = [
        'id'       => '',
        'title'    => '',
        'tabs'     => [],
        'dropDown' => [],
    ];

    public function __construct()
    {
        $this->class('nav-tabs-custom');
    }

    /**
     * Add a tab and its contents.
     *
     * @param string            $title
     * @param string|Renderable $content
     *
     * @return $this
     */
    public function add($title, $content)
    {
        $this->data['tabs'][] = [
            'id'      => mt_rand(),
            'title'   => &$title,
            'content' => &$content,
        ];

        return $this;
    }

    /**
     * Set title.
     *
     * @param string $title
     */
    public function title($title = '')
    {
        $this->data['title'] = $title;
    }

    /**
     * Set drop-down items.
     *
     * @param array $links
     *
     * @return $this
     */
    public function dropDown(array $links)
    {
        if (is_array($links[0])) {
            foreach ($links as &$link) {
                call_user_func([$this, 'dropDown'], $link);
            }

            return $this;
        }

        $this->data['dropDown'][] = [
            'name' => $links[0],
            'href' => $links[1],
        ];

        return $this;
    }

    /**
     * Render Tab.
     *
     * @return string
     */
    public function render()
    {
        $this->data['attributes'] = $this->formatAttributes();

        return view($this->view, $this->data)->render();
    }
}
