<?php

namespace Lxh\Admin\Widgets;

use Lxh\Admin\Admin;
use Lxh\Admin\Tools\Tools;
use Lxh\Contracts\Support\Renderable;
use Lxh\Helper\Util;

class Box extends Widget implements Renderable
{
    public static $scripts = [];

    protected static $loadScripts = false;

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
     * @var Tools
     */
    protected $leftTools;

    /**
     * @var Tools
     */
    protected $rightTools;

    /**
     * @var string
     */
    protected $toolClass = '';

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
//        $this->style('success');

        Admin::addScriptClass(__CLASS__);
    }

    public function setTools(Tools $tools)
    {
        $this->leftTools = $tools;
        return $this;
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
        $this->content = &$content;

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
        $this->title = &$title;

        return $this;
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
        $this->id = Util::randomString();

        $this->rightTools()->append(
            "<a id='collapse-{$this->id}' data-toggle=\"collapse\" href=\"#{$this->id}\"><i class=\"zmdi zmdi-minus\"></i></a>"
        );

        return $this;
    }

    /**
     * Set box as removable.
     *
     * @return $this
     */
    public function removable()
    {
        $this->rightTools()->append(
            '<a data-toggle="remove"><i class="zmdi zmdi-close"></i></a>'
        );

        static::$scripts[0] = <<<EOF
            $('.portlet [data-toggle="remove"]').click(function (e) {
                $(this).parent().parent().parent().parent().parent().toggle(100)
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
        static::$scripts[1] = <<<EOF
            $('#collapse-{$this->id}').trigger('click')
EOF;
        return $this;
    }

    /**
     * @return Tools
     */
    public function rightTools()
    {
        if (! $this->rightTools) {
            $this->rightTools = new Tools();
            $this->rightTools->setDelimiter('');
        }

        return $this->rightTools;
    }

    /**
     * @return Tools
     */
    public function leftTools()
    {
        if (! $this->leftTools) {
            $this->leftTools = new Tools();
        }

        return $this->leftTools;
    }

    /**
     * @param $action
     * @return $this
     */
    public function setAction($action)
    {
        $this->actions[] = &$action;
        return $this;
    }

    public function backable()
    {
        $this->rightTools()->append(
            '<button data-toggle="back" type="button" class="btn btn-default waves-effect"><i class="ti-arrow-left"></i>&nbsp;&nbsp;'
            . trans('back') . '</button>'
        );

        static::$scripts[2] = '$(\'.portlet [data-toggle="back"]\').click(back_tab);';
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

        $this->class('box-border');

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
        $content = $this->content instanceof Renderable ? $this->content->render() : $this->content;

        return [
            'title'      => $this->title,
            'content'    => &$content,
            'tools'      => $this->rightTools()->render(),
            'attributes' => $this->formatAttributes(),
            'id' => $this->id,
            'actions' => $this->leftTools()->render(),
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
        if (!static::$loadScripts) {
            static::$loadScripts = true;

            // 点击时重新计算高度
            Admin::script(<<<EOF
(function () {
var c = LXHSTORE.IFRAME.current();
$('[data-toggle="collapse"]').click(function () {
   setTimeout(function(){LXHSTORE.IFRAME.height(c);},250);
});
})();
EOF
            );
        }

        return view($this->view, $this->variables())->render();
    }
}
