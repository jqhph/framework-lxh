<?php

namespace Lxh\Admin\Layout;

use Closure;
use Lxh\Admin\Admin;
use Lxh\Admin\Filter;
use Lxh\Admin\Grid;
use Lxh\Admin\Widgets\Box;
use Lxh\Admin\Widgets\Form;
use Lxh\Admin\Widgets\Modal;
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
     * @var string
     */
    protected $view = 'admin::content';

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
     * @return $this
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

    /**
     * @return $this
     */
    public function independent()
    {
        $this->view = 'admin::indie-content';
        return $this;
    }

    /**
     * 表单
     *
     * @param $callback
     * @return Box
     */
    public function form(callable $callback = null, $width = 12)
    {
        $row = new Row();

        $form = new Form();

        $box = new Box(null, $form);

        $column = $row->column($width, $box->backable());

        if ($callback) {
            call_user_func($callback, $form, $column);
        }

        $this->addRow($row);

        return $box;
    }

    /**
     * @param null $title
     * @param Closure|null $callback
     */
    public function modal($title = null, Closure $callback = null)
    {
        $modal = new Modal($title);

        if ($callback) {
            $callback($modal);
        }

        $row = new Row();
        $row->column(12, $modal);
        $this->addRow($row);

        return $modal;
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
     * @return $this
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

        return $this;
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
        // 异步加载table，无需加载整个内容
        if (Grid::isPjaxRequest()) {
            // 必须先调用build方法
            // 否则可能导致加载不到相关js
            $content = $this->build();

            $script = Admin::script();
            $js = Admin::js();
            $css = Admin::css();
            $asyncJs = Admin::async();

            return "{$content}{$css}{$asyncJs}{$js}<script>{$script}</script>";
        }

        Admin::collectFieldAssets();
        $items = [
            'header'      => $this->header,
            'description' => $this->description,
            'content'     => $this->build(),
            'js'          => Admin::js(),
            'css'         => Admin::css(),
            'script'      => Admin::script(),
            'asyncJs'     => Admin::async(),
        ];

        return view($this->view, $items)->render();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }
}
