<?php

namespace Lxh\Admin\Widgets;

use Lxh\Admin\Admin;
use Lxh\Contracts\Support\Renderable;

class Box extends Widget implements Renderable
{
    public static $script = [];

    /**
     * @var string
     */
    protected $view = 'admin::widget.box';

    /**
     * @var string
     */
    protected $title = '';

    /**
     * @var string
     */
    protected $content = '';

    /**
     * @var array
     */
    protected $tools = [];

    protected $toolClass = '';

    protected $actions = [];

    /**
     * @var mixed
     */
    protected $id;

    /**
     * Box constructor.
     *
     * @param string $title
     * @param string $content
     */
    public function __construct($title = '', $content = '')
    {
        if ($title) {
            $this->title($title);
        }

        if ($content) {
            $this->content($content);
        }

        $this->class('box portlet');
        $this->style('success');

        Admin::addScriptClass(__CLASS__);
    }

    /**
     * Set box content.
     *
     * @param string $content
     *
     * @return $this
     */
    public function content($content)
    {
        if ($content instanceof Renderable) {
            $this->content = $content->render();
        } else {
            $this->content = &$content;
        }

        return $this;
    }

    /**
     * Set box title.
     *
     * @param string $title
     *
     * @return $this
     */
    public function title($title)
    {
        $this->title = $title;

        return $this;
    }

    protected function generateId()
    {
        return md5(uniqid(microtime(true)));
    }

    public function toolClass($class = null)
    {
        if ($class != null) {
            $this->toolClass = $class;
            return $this;
        }
        return $this->toolClass;
    }

    public function btnToolbar()
    {
        return $this->toolClass('btn-toolbar');
    }

    /**
     * Set box as collapsable.
     *
     * @return $this
     */
    public function collapsable()
    {
        $this->id = $this->generateId();

        $this->tools[] = "<a id='collapse-{$this->id}' data-toggle=\"collapse\" href=\"#{$this->id}\"><i class=\"zmdi zmdi-minus\"></i></a>";

        return $this;
    }

    /**
     * Set box as removable.
     *
     * @return $this
     */
    public function removable()
    {
        $this->tools[] = '<a data-toggle="remove"><i class="zmdi zmdi-close"></i></a>';

        static::$script[0] = <<<EOF
            $('.portlet [data-toggle="remove"]').click(function (e) {
                $(this).parent().parent().parent().toggle(100)
            })
EOF;
        return $this;
    }

    /**
     * 默认隐藏
     *
     * @return static
     */
    public function slideUp()
    {
        static::$script[0] = <<<EOF
            $('#collapse-{$this->id}').trigger('click')
EOF;
        return $this;
    }

    public function tool($tool)
    {
        $this->tools[] = &$tool;
        return $this;
    }

    public function action($action)
    {
        $this->actions[] = &$action;
        return $this;
    }

    public function backable()
    {
        $this->tools[] = '<button data-toggle="back" type="button" class="btn btn-default waves-effect"><i class="ti-arrow-left"></i>&nbsp;&nbsp;'
            . trans('back') . '</button>';

        static::$script[1] = <<<EOF
             $('.portlet [data-toggle="back"]').click(function(){back_tab();})
EOF;
        return $this;
    }

    /**
     * Set box style.
     *
     * @param string $styles
     *
     * @return $this|Box
     */
    public function style($styles)
    {
        if (is_string($styles)) {
            return $this->style([$styles]);
        }

        $styles = array_map(function ($style) {
            return 'box-'.$style;
        }, $styles);

        $this->class = $this->class.' '.implode(' ', $styles);
        
        return $this;
    }

    /**
     * Add `box-solid` class to box.
     *
     * @return $this
     */
    public function solid()
    {
        return $this->style('solid');
    }

    /**
     * Variables in view.
     *
     * @return array
     */
    protected function variables()
    {
        return [
            'title'      => $this->title,
            'content'    => $this->content,
            'tools'      => $this->tools,
            'attributes' => $this->formatAttributes(),
            'id' => $this->id,
            'actions' => $this->actions,
            'toolClass' => $this->toolClass(),
        ];
    }

    /**
     * Render box.
     *
     * @return string
     */
    public function render()
    {
        return view($this->view, $this->variables())->render();
    }
}
