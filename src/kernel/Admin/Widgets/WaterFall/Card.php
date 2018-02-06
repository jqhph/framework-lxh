<?php

namespace Lxh\Admin\Widgets\WaterFall;

use Lxh\Contracts\Support\Renderable;

class Card
{
    /**
     * @var string
     */
    protected $image;

    /**
     * @var array
     */
    protected $rows = [];

    /**
     * @var array
     */
    protected $filters = [];

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $meta;

    public function __construct($title = '', array $filters = [])
    {
        $this->title = &$title;
        $this->filters = &$filters;
    }

    /**
     * @param $image
     * @return $this
     */
    public function image($image)
    {
        if ($image instanceof Renderable) {
            $image = $image->render();
        } elseif ($image instanceof \Closure) {
            $image = $image($this);
        }
        $this->image = &$image;
        return $this;
    }

    /**
     * @param array $filters
     * @return $this
     */
    public function setFilters(array $filters)
    {
        $this->filters = &$filters;
        return $this;
    }

    /**
     * @return array
     */
    public function filters()
    {
        return $this->filters;
    }

    /**
     * 设置title
     *
     * @param $title
     * @return $this
     */
    public function title($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @param $meta
     * @return $this
     */
    public function meta($meta)
    {
        $this->rows[] = "<div class='meta'>$meta</div>";
        return $this;
    }

    /**
     * @param $row
     * @param null $right
     * @return $this
     */
    public function row($row, $right = null)
    {
        if ($row instanceof Renderable) {
            $row = $row->render();
        } elseif ($row instanceof \Closure) {
            $row = $row($this);
        }

        if ($right) {
            $right = "<span class='pull-right'>$right</span>";
        }
        $this->rows[] = "<div class='row'>{$row}{$right}</div>";
        return $this;
    }

    /**
     * @return string
     */
    public function render()
    {
        $image = '';
        if ($this->image) {
            $image = "<div class='img'>{$this->image}</div>";
        }

        $title = '';
        if ($this->title) {
            $title = "<strong>{$this->title}</strong>";
        }

        $rows = implode('', $this->rows);

        return "{$image}{$title}<div class=\"content\">$rows</div>";
    }

}
