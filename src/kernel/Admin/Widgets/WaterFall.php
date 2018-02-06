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
    protected $cards = [];

    /**
     * @var array
     */
    protected $options = [
        'itemWidth' => 200,
        'offset' => 10,
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
     * @param mixed $content
     * @param array $filterClasses
     */
    public function card($content, array $filterClasses = [])
    {
        if (is_callable($content)) {
            $card['content'] = call_user_func($content, $this);
        } else {
            $card['content'] = &$content;
        }
        $card['filters'] = &$filterClasses;

        $this->cards[] = &$card;

        return $this;
    }

    /**
     * @param array $cards
     * @return $this
     */
    public function setCards(array $cards)
    {
        $this->cards = &$cards;
        return $this;
    }

    /**
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
            'cards' => &$this->cards,
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
