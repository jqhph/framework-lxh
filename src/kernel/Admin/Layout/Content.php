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
     * @var array
     */
    protected $prepend = [];

    /**
     * @var array
     */
    protected $append = [];

    protected $options = [
        'page-default-assets' => false,
    ];

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
        $this->header = &$header;

        return $this;
    }

    public function prepend($prepend)
    {
        $this->prepend[] = &$prepend;

        return $this;
    }

    public function append($append)
    {
        $this->append[] = &$append;

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
        $this->description = &$description;

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
        if (is_callable($content)) {
            $this->rows[] = $row = new Row();
            call_user_func($content, $row);
        } else {
            $this->rows[] = new Row($content);
        }

        return $this;
    }

    /**
     * 独立的页面
     *
     * @param bool $useDefaultAssets 是否加载预定义js和css
     * @return $this
     */
    public function page($useDefaultAssets = false)
    {
        $this->view = 'admin::page';

        $this->options['page-default-assets'] = $useDefaultAssets;

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
        $this->rows[] = $row = new Row();

        $form = new Form();

        $box = new Box(null, $form);

        $column = $row->column($width, $box->backable());

        if ($callback) {
            call_user_func($callback, $form, $column);
        }

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

        $this->rows[] = $row = new Row();
        $row->column(12, $modal);

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
        $this->rows[] = $row = new Row();

        // 网格
        $grid = new Grid();

        // 添加列
        $column = $row->column($width, $grid);

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
            $this->rows[] = $row = new Row();
            call_user_func($content, $row);
        } else {
            $this->rows[] = new Row($content);
        }

        return $this;
    }

    /**
     * Build html of content.
     *
     * @return string
     */
    public function &build()
    {
        ob_start();

        foreach ($this->rows as &$row) {
            $row->build();
        }

        $contents = implode('', $this->prepend).ob_get_contents().implode('', $this->append);

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
        // 必须先调用build方法
        $content = $this->build();

        // 注入所有字段静态资源
        Admin::collectFieldAssets();

        $html    = Admin::hidden();
        $js      = Admin::js();
        $css     = Admin::css();
        $script  = Admin::script();
        $syncJs  = Admin::getLoadScripts();
        $syncCss = Admin::getLoadStyles();


        // 异步加载table，无需加载整个内容
        if (Grid::isPjaxRequest()) {
            return "{$content}{$syncCss}{$syncJs}<script>{$css}{$js}{$script}</script><div style='display:none'>{$html}</div>";
        }

        return view(
            $this->view,
            [
                'header'           => &$this->header,
                'description'      => &$this->description,
                'content'          => &$content,
                'hidden'           => &$html,
                'js'               => &$js,
                'css'              => &$css,
                'script'           => &$script,
                'style'            => Admin::style(),
                'loadscss'         => &$syncCss,
                'loadscripts'      => &$syncJs,
                'useDefaultAssets' => $this->options['page-default-assets'],
            ]
        )->render();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }
}
