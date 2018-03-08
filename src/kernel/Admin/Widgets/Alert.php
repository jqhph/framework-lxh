<?php

namespace Lxh\Admin\Widgets;

use Lxh\Contracts\Support\Renderable;

class Alert extends Widget implements Renderable
{
    /**
     * @var string
     */
    protected $view = 'admin::widget.alert';

    /**
     * @var string
     */
    protected $title = '';

    /**
     * @var string
     */
    protected $content = '';

    /**
     * @var string
     */
    protected $style = 'danger';

    /**
     * @var string
     */
    protected $icon = 'ban';

    protected $closeable = true;

    /**
     * Alert constructor.
     *
     * @param mixed  $content
     * @param string $title
     * @param string $style
     */
    public function __construct($content = '', $title = '', $style = 'danger')
    {
        $this->content = &$content;

        $this->title = $title;

        $this->style($style);
    }

    public function primary()
    {
        $this->style = 'primary';
        $this->icon = '';

        return $this;
    }

    public function disabledColse()
    {
        $this->closeable = false;
        return $this;
    }

    /**
     * Add style.
     *
     * @param string $style
     *
     * @return $this
     */
    public function style($style = 'info')
    {
        $this->style = $style;
        if ($style == 'primary') {
            $this->icon = '';
        }

        return $this;
    }

    /**
     * Add icon.
     *
     * @param string $icon
     *
     * @return $this
     */
    public function icon($icon)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * @return array
     */
    protected function variables()
    {
        $this->class("alert alert-{$this->style} alert-dismissable");

        return [
            'title'      => $this->title,
            'content'    => $this->content,
            'icon'       => $this->icon,
            'attributes' => $this->formatAttributes(),
            'closeable'  => $this->closeable,
        ];
    }

    /**
     * Render alter.
     *
     * @return string
     */
    public function render()
    {
        return view($this->view, $this->variables())->render();
    }
}
