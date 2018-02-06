<?php

namespace Lxh\Admin\Widgets;

use Lxh\Admin\Admin;
use Lxh\Admin\Widgets\WaterFall\Card;
use Lxh\Contracts\Support\Renderable;

class WaterFall extends Widget implements Renderable
{
    /**
     * @var string
     */
    protected $view = 'admin::widget.water-fall';

    /**
     * @var array
     */
    protected $items = [];

    /**
     * @var array
     */
    protected $options = [
        'itemWidth' => 200,
        'offset' => 12,
        'align' => 'center',
        'autoResize' => true,
        'fillEmptySpace' => false,
    ];

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);

        Admin::css('@lxh/css/water-fall');
        Admin::js('@lxh/js/jquery.wookmark.min');
    }

    public function filters()
    {

    }

    /**
     * @param int $width
     * @return $this
     */
    public function width($width)
    {
        $this->options['itemWidth'] = $width;
        return $this;
    }

    /**
     * @param string $align
     * @return $this
     */
    public function align($align)
    {
        $this->options['align'] = $align;
        return $this;
    }

    /**
     *
     * @param mixed $content
     * @param array $filterClasses
     * @return $this
     */
    public function card($content)
    {
        $card = new Card();
        if (is_callable($content)) {
            $content($card, $this);
        } else {
            $card->row($content);
        }

        return $this->item(
            $card->render(), $card->filters()
        );
    }

    /**
     *
     * @param mixed $content
     * @param array $filterClasses
     * @return $this
     */
    public function item($content, array $filterClasses = [])
    {
        if (is_callable($content)) {
            $item['content'] = call_user_func($content, $this);
        } else {
            $item['content'] = &$content;
        }
        $item['filters'] = &$filterClasses;

        $this->items[] = &$item;
        return $this;
    }

    /**
     * 设置间距
     *
     * @param $offset
     * @return $this
     */
    public function offset($offset)
    {
        $this->options['offset'] = $offset;
        return $this;
    }

    protected function vars()
    {
        return [
            'options' => &$this->options,
            'items' => &$this->items,
        ];
    }

    /**
     * @return string
     */
    public function render()
    {
        return view($this->view, $this->vars())->render();
    }
}
