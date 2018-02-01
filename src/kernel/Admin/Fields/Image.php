<?php

namespace Lxh\Admin\Fields;

use Lxh\Admin\Admin;
use Lxh\Contracts\Support\Renderable;

class Image extends Field
{
    protected static $gridImgScript = null;

    /**
     * @var bool
     */
    protected $useModal = true;

    public function render()
    {
        if (empty($this->value)) {
            return '';
        }

        if (static::$gridImgScript === null && $this->useModal) {
            static::$gridImgScript = 1;
            Admin::script(<<<EOF
function show_img() {
   var s = /style=[\'\"]?([^\'\"]*)[\'\"]?/i, c = $(this).html().replace(s,'');
   var m = \$lxh.ui().modal({title: '图片', confirmBtn: false, content: '<a href="{$this->value}" target="_blank">' + c + '</a>'});
   m.modal('show');
}
$('.grid-img').click(show_img);
EOF
);
        }

        if (empty($this->attributes['style'])) {
            $this->style('max-width:150px');
        }

        $this->class('img img-thumbnail');

        return "<a class='grid-img'><img src=\"{$this->value}\" {$this->formatAttributes()}/></a>";

    }

    /**
     * @return $this
     */
    public function disableModal()
    {
        $this->useModal = false;
        return $this;
    }

    public function width($width)
    {
        return $this->style('width:' . $width);
    }
}
