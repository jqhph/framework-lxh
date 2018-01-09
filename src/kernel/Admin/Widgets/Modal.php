<?php

namespace Lxh\Admin\Widgets;

use Lxh\Admin\Fields\Button;
use Lxh\Admin\Tools\Tools;
use Lxh\Contracts\Support\Renderable;

class Modal extends Widget implements Renderable
{
    /**
     * @var string
     */
    protected $view = 'admin::widget.modal';

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $animate = 'fade';

    /**
     * @var mixed
     */
    protected $content;

    /**
     * @var array
     */
    protected $options = [
        'closeBtn' => true,
        'confirmBtn' => true,
    ];

    /**
     * @var string
     */
    protected $width = '';

    /**
     * @var Tools
     */
    protected $tools;

    public function __construct($title = null, $content = null)
    {
        $this->title = &$title;
        $this->content = &$content;

        $this->tools = new Tools();
    }

    protected function setupTools()
    {
        if ($this->options['closeBtn']) {
            $close = new Button(trans('Close'));
            $this->tools->append(
                $close->color('default')->attribute('data-dismiss', 'modal')
            );
        }
    }

    /**
     * @param $title
     * @return $this
     */
    public function title($title)
    {
        $this->title = &$title;

        return $this;
    }

    /**
     * @param $content
     * @return $this
     */
    public function content($content)
    {
        $this->content = &$content;

        return $this;
    }

    public function disableCloseBtn()
    {
        $this->options['closeBtn'] = false;

        return $this;
    }

    protected function buildFooter()
    {
        return $this->tools->render();
    }

    /**
     * @return Tools
     */
    public function tools()
    {
        return $this->tools;
    }

    /**
     * 定义弹窗宽度
     * 单位为百分比或px
     *
     * @param $width
     * @return $this
     */
    public function width($width)
    {
        $this->width = $width;

        return $this;
    }

    public function render()
    {
        $this->class('modal ' . $this->animate);

        $content = &$this->content;
        if ($content instanceof Renderable) {
            $content = $content->render();
        } elseif ($content instanceof \Closure) {
            $content = $content($this);
        }

        $this->setupTools();
        
        return view(
            $this->view,
            [
                'attributes' => $this->formatAttributes(),
                'title' => $this->title,
                'body' => &$content,
                'footer'  => $this->buildFooter(),
                'style' => $this->formatWidth(),
            ]
        )
            ->render();
    }

    protected function formatWidth()
    {
        if ($this->width) {
            return "width:{$this->width};";
        }
    }

}
