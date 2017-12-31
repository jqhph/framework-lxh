<?php

namespace Lxh\Admin\Layout;

use Closure;
use Lxh\Admin\Admin;
use Lxh\Admin\Filter;
use Lxh\Admin\Grid;
use Lxh\Contracts\Support\Renderable;
use Lxh\Support\MessageBag;

class Content implements Renderable
{
    /**
     * Content header.
     *
     * @var string
     */
    protected $header = '';

    /**
     * Content description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * @var Row[]
     */
    protected $rows = [];

    /**
     * Content constructor.
     *
     * @param Closure|null $callback
     */
    public function __construct(\Closure $callback = null)
    {
        if ($callback) {
            $callback($this);
        }
    }

    /**
     * Set header of content.
     *
     * @param string $header
     *
     * @return $this
     */
    public function header($header = '')
    {
        $this->header = $header;

        return $this;
    }

    /**
     * Set description of content.
     *
     * @param string $description
     *
     * @return $this
     */
    public function description($description = '')
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Alias of method row.
     *
     * @param mixed $content
     *
     * @return Content
     */
    public function body($content)
    {
        return $this->row($content);
    }

    /**
     * 过滤器
     *
     * @param $callback
     * @return Filter
     */
    public function filter(callable $callback = null, $width = 12)
    {
        $row = new Row();

        $filter = new Filter();

        $column = $row->column($width, $filter);

        $this->addRow($row);

        if ($callback) {
            call_user_func($callback, $filter, $column);
        }
        
        return $filter;
    }

    public function modal()
    {
        
    }

    /**
     * 创建网格报表
     *
     * @return Grid
     */
    public function grid($params = null, $width = 12)
    {
        // 行
        $row = new Row();

        // 网格
        $grid = new Grid();

        // 添加列
        $column = $row->column($width, $grid);

        // 添加行
        $this->addRow($row);

        if (is_array($params)) {
            // 添加网格配置
            $grid->headers($params);
        } elseif ($params && is_callable($params)) {
            // 外部回调
            call_user_func($params, $grid, $column);
        }

        return $grid;
    }

    /**
     * Add one row for content body.
     *
     * @param $content
     *
     * @return Row
     */
    public function row($content = '')
    {
        if (is_callable($content)) {
            $row = new Row();
            call_user_func($content, $row);
            $this->addRow($row);
        } else {
            $row = new Row($content);
            $this->addRow($row);
        }

        return $row;
    }

    /**
     * Add Row.
     *
     * @param Row $row
     */
    protected function addRow(Row $row)
    {
        $this->rows[] = $row;
    }

    /**
     * Build html of content.
     *
     * @return string
     */
    public function build()
    {
        ob_start();

        foreach ($this->rows as &$row) {
            $row->build();
        }

        $contents = ob_get_contents();

        ob_end_clean();

        return $contents;
    }

    /**
     * Set error message for content.
     *
     * @param string $title
     * @param string $message
     *
     * @return $this
     */
    public function withError($title = '', $message = '')
    {
        $error = new MessageBag(compact('title', 'message'));

        return $this;
    }

    /**
     * Render this content.
     *
     * @return string
     */
    public function render()
    {
        Admin::collectFieldAssets();
        $items = [
            'header'      => $this->header,
            'description' => $this->description,
            'content'     => $this->build(),
            'js'          => Admin::js(),
            'css'         => Admin::css(),
            'script'      => Admin::script(),
        ];

        return view('admin::content', $items)->render();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }
}
