<?php

namespace Lxh\Admin\Widgets;

use Lxh\Admin\Admin;

class Collapse extends Widget
{
    protected static $loadScripts = false;

    /**
     * @var string
     */
    protected $view = 'admin::widget.collapse';

    /**
     * @var array
     */
    protected $items = [];

    /**
     * Collapse constructor.
     */
    public function __construct(array $items = [])
    {
        $this->id('accordion-'.uniqid());
        $this->class('panel-group m-b-0');
        $this->style('margin-bottom: 20px');

        $this->items = &$items;
    }

    /**
     * Add item.
     *
     * @param string $title
     * @param string $content
     * @param bool   $show
     * @return $this
     */
    public function add($title, $content, $show = false)
    {
        $this->items[] = [
            'title'   => &$title,
            'content' => &$content,
            'show'    => $show
        ];

        return $this;
    }

    protected function variables()
    {
        return [
            'id'         => $this->id,
            'items'      => &$this->items,
            'attributes' => $this->formatAttributes(),
        ];
    }

    /**
     * Render Collapse.
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
if (typeof LXHSTORE.IFRAME == 'undefined') return false;
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
