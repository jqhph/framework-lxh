<?php

namespace Lxh\Admin\Fields;

use Lxh\Admin\Admin;
use Lxh\Contracts\Support\Renderable;
use Lxh\Helper\Util;

class Image extends Field
{
    /**
     * @var bool
     */
    protected $useModal = true;

    public function render()
    {
        if (empty($this->value)) {
            return '';
        }

        $title = trans('Image');
        $this->script('gimg', <<<EOF
$('{$this->getContainerIdSelector()}').find('.grid-img').click(function(){
   var t=$(this),s= /style=[\'\"]?([^\'\"]*)[\'\"]?/i, c = t.html().replace(s,''), u=t.find('img').attr('src'),
   m= \$lxh.ui().modal({id:t.attr('id'),title:'$title',confirmBtn:false,content:'<a href="'+ u +'" target="_blank">'+ c +'</a>'});
   m.modal('show');
});
EOF
);


        if (empty($this->attributes['style'])) {
            $this->style('max-width:150px');
        }

        $this->class('img img-thumbnail');

        $id = Util::randomString();
        return "<a class='grid-img' id='{$id}'><img src=\"{$this->value}\" {$this->formatAttributes()}/></a>";

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
