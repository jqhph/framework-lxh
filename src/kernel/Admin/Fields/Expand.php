<?php


namespace Lxh\Admin\Fields;

use Lxh\Admin\Admin;
use Lxh\Contracts\Support\Renderable;
use Lxh\Helper\Util;

class Expand extends Field
{
    /**
     * @var string
     */
    protected $content;

    /**
     * @param $color
     * @return $this|mixed
     */
    public function color($color)
    {
        return $this->option('color', $color);
    }

    public function render()
    {
        $this->script('expand', "$('.grid-expand').click(function(){var t = $(this), i=t.find('i');i.toggleClass('fa-caret-right');i.toggleClass('fa-caret-down')});");

        $color = $this->option('color') ?: 'custom';

        $id = Util::randomString();

        // 增加一行
        $this->tr->next("<div id=\"$id\" class=\"panel-collapse collapse out\">{$this->content}</div>");

        return "<a class=\"btn btn-xs btn-$color grid-expand\" data-toggle=\"collapse\" data-target=\"#$id\"><i class=\"fa fa-caret-right\"></i> {$this->label()}</a>";
    }

    /**
     * @param $content
     * @return $this
     */
    public function content($content)
    {
        if ($content instanceof $content) {
            $this->content = $content($this, $this->tr);
        } else {
            $this->content = $content;
        }

        return $this;
    }
}
