<?php

namespace Lxh\Admin\Table;

use Lxh\Admin\Table\Table;
use Lxh\Admin\Widgets\Widget;
use Lxh\Contracts\Support\Renderable;
use Lxh\Support\Arr;

class Column extends Widget
{
    protected $defaultTitle = '&nbsp;';

    /**
     * @var Th|mixed
     */
    protected $th;

    /**
     * 当前行数据
     *
     * @var array
     */
    protected $row = [];

    /**
     * @var string
     */
    protected $content = '';

    protected $callback;
    
    public function __construct($title, $content = null)
    {
        if (is_callable($title) && $content === null) {
            $this->th = new Th(null, $this->defaultTitle);

            return $this->content = $title;
        }

        $this->th = new Th(null, $title ?: $this->defaultTitle);
        $this->content = $content;
    }

    /**
     * 设置或获取当前行数据
     *
     * @param array $row
     * @return static | array
     */
    public function row(array $row = null)
    {
        if ($row !== null) {
            $this->row = &$row;

            return $this;
        }
        return $this->row;
    }

    /**
     * 获取列标题
     *
     * @return string
     */
    public function title()
    {
        return $this->th->render();
    }

    public function render()
    {
        if (is_callable($this->content)) {
            return call_user_func($this->content, $this->row, $this, $this->th);
        }
        return $this->content;
    }
}
