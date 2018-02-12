<?php

namespace Lxh\Admin\Widgets\WaterFall;

use Lxh\Admin\Fields\Image;
use Lxh\Contracts\Support\Renderable;

class Card
{
    /**
     * @var array
     */
    protected $top = [];

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
     * 在卡片顶部插入图片
     *
     * @param $image
     * @return $this
     */
    public function image($src)
    {
        $image = new Image();
        $image->width('auto')->value($src);

        return $this->top($image);
    }

    /**
     * 卡片顶部内容
     *
     * @param $value
     * @return $this
     */
    public function top($value)
    {
        if ($value instanceof Renderable) {
            $value = $value->render();
        } elseif ($value instanceof \Closure) {
            $value = $value($this);
        }
        
        $this->top[] = "<div class='top-content'>$value</div>";
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
        if ($title instanceof Renderable) {
            $title = $title->render();
        } elseif ($title instanceof \Closure) {
            $title = $title($this);
        }

        $this->title = &$title;
        return $this;
    }

    /**
     * @param $meta
     * @return $this
     */
    public function meta($meta)
    {
        if ($meta instanceof Renderable) {
            $meta = $meta->render();
        } elseif ($meta instanceof \Closure) {
            $meta = $meta($this);
        }
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
            if ($right instanceof Renderable) {
                $right = $right->render();
            } elseif ($right instanceof \Closure) {
                $right = $right($this);
            }
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
        $title = '';
        if ($this->title) {
            $title = "<strong>{$this->title}</strong>";
        }

        $top = implode('', $this->top);
        $rows = implode('', $this->rows);

        return "{$top}{$title}<div class=\"content\">$rows</div>";
    }

}
