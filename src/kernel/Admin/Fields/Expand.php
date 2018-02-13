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
     * @var null
     */
    protected $ajax = null;

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
        $this->setupScript();

        $color = $this->option('color') ?: 'default';

        $id = Util::randomString();

        // 增加一行
        $this->table->addExtraRow("<div id=\"$id\" class=\"panel-collapse collapse out\">{$this->content}</div>");

        return "<a class=\"btn btn-xs btn-$color grid-expand\" data-toggle=\"collapse\" data-target=\"#$id\"><i class=\"fa fa-caret-right\"></i> {$this->label()}</a>";
    }

    protected function setupScript()
    {
        $script = '';
        if ($this->ajax) {
            $script = <<<EOF
if (t.attr('ajax')) return; var \$c =$(t.data('target'));t.button('loading');t.attr('ajax',1);NProgress.start();
$.get('$this->ajax',function(d){
NProgress.done();
    if (d) 
       \$c.html(d);
    else 
       \$c.html('{$this->noDataTip()}');
setTimeout(function(){t.button('reset')},200)
});
EOF;
        }
        $this->script('expand', <<<SCRIPT
$('.grid-expand').click(function(){
var t=$(this),i=t.find('i');i.toggleClass('fa-caret-right');i.toggleClass('fa-caret-down');{$script}
});        
SCRIPT
);
    }

    /**
     * @return string
     */
    protected function noDataTip()
    {
        $tip = trans('No Data.');
        return <<<EOF
<table class="table"><tr><td><div style="margin:15px 0 0 25px;"><span class="help-block" style="margin-bottom:0"><i class="fa fa-info-circle"></i>&nbsp;{$tip}</span></div></td></tr></table>
EOF;

    }

    /**
     * 发送ajax请求获取内容
     *
     * @param $api
     * @return $this
     */
    public function ajax($api)
    {
        $this->ajax = &$api;
        $this->content = ' ';
        return $this;
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
