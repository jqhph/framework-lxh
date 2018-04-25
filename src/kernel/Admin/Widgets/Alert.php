<?php

namespace Lxh\Admin\Widgets;

use Lxh\Contracts\Support\Renderable;

class Alert extends Widget implements Renderable
{
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
    protected $icon = 'fa fa-ban';

    protected $closeable = true;

    /**
     * Alert constructor.
     *
     * @param mixed  $content
     * @param string $title
     * @param string $style
     */
    public function __construct($content = '', $title = '', $style = 'primary')
    {
        $this->content = &$content;
        $this->title   = $title;

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
     * Render alter.
     *
     * @return string
     */
    public function render()
    {
        $this->class("alert alert-{$this->style} alert-dismissable");

        if ($this->content instanceof Renderable) {
            $this->content = $this->content->render();
        }

        $close = $icon = '';
        if ($this->closeable) {
            $close = '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
        }
        $title = $this->title;
        if ($title) {
            if ($this->icon) {
                $title = "<h4><i class='icon {$this->icon}'></i> $title</h4>";
            } else {
                $title = "<h4>$title</h4>";
            }
        }

        return "<div {$this->formatAttributes()}>{$close}{$title}{$this->content}</div>";
    }
}
