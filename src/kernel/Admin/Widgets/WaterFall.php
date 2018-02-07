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
    protected $filters = [];

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
        'align' => 'left',
        'autoResize' => true,
        'fillEmptySpace' => false,
    ];

    /**
     * @var string
     */
    protected $filterMode = 'and';

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);

        Admin::css('@lxh/css/water-fall');
        Admin::js('@lxh/js/jquery.wookmark.min');
    }

    /**
     *
     * @param array $filters
     * @return $this
     */
    public function filters(array $filters = [])
    {
        foreach ($filters as $k => &$v) {
            if (is_array($v) && ! empty($v['label'])) {
                continue;
            }
            $value = $v;
            if (is_string($k)) {
                $v = [
                    'value' => $value,
                    'label' => $k
                ];
                continue;
            }
            $v = [
                'value' => $value,
                'label' => trans($value),
            ];
        }

        $this->filters = &$filters;
        return $this;
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
     * @return $this
     */
    public function left()
    {
        $this->options['align'] = 'left';
        return $this;
    }

    /**
     * @return $this
     */
    public function center()
    {
        $this->options['align'] = 'center';
        return $this;
    }

    /**
     * @return $this
     */
    public function filterAnd()
    {
        $this->filterMode = 'and';
        return $this;
    }

    /**
     * @return $this
     */
    public function filterOr()
    {
        $this->filterMode = 'or';
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
            'filterMode' => $this->filterMode,
            'filters' => &$this->filters,
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
