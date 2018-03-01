<?php

namespace Lxh\Admin\Layout;

use Lxh\Contracts\Support\Renderable;

class Column
{
    /**
     * @var int
     */
    protected $width = 12;

    /**
     * @var array
     */
    protected $contents = [];

    /**
     * Column constructor.
     *
     * @param $content
     * @param int $width
     */
    public function __construct($content, $width = 12)
    {
        if ($content instanceof \Closure) {
            call_user_func($content, $this);
        } else {
            $this->contents[] = &$content;
        }

        $this->width = $width;
    }

    /**
     * Append content to column.
     *
     * @param $content
     *
     * @return $this
     */
    public function append($content)
    {
        $this->contents[] = &$content;

        return $this;
    }

    /**
     * Add a row for column.
     *
     * @param $content
     *
     * @return Row
     */
    public function row($content = '')
    {
        return $this->contents[] = new Row($content);
    }

    /**
     * Build column html.
     */
    public function build()
    {
        echo "<div class=\"col-md-{$this->width}\">";

        foreach ($this->contents as &$content) {
            if ($content instanceof Renderable) {
                echo $content->render();
            } elseif ($content instanceof Row) {
                echo $content->build();
            } else {
                echo $content;
            }
        }

        echo '</div>';
    }

}
