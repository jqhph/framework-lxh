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
     * @var array
     */
    protected $post = [];

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
        $id = 'e'.Util::randomString();

        $this->setupScript($id);

        $color = $this->option('color') ?: 'default';

        $action = '';
        if (!$this->ajax) {
            $action = 'data-toggle="collapse"';
        }

        // 增加一行
        $this->table->addExtraRow("<div id=\"$id\" class=\"panel-collapse collapse\">{$this->content}</div>");

        return "<a class=\"btn btn-xs btn-$color grid-expand\" $action data-target=\"#$id\"><i class=\"fa fa-caret-right\"></i> {$this->label()}</a>";
    }

    protected function setupScript($id)
    {
        $script = '';

        $post = json_encode($this->post);

        if ($this->ajax) {
            $script = <<<EOF
var targ = t.data('target'), \$c =$(targ);t.button('loading');t.attr('ajax',1);NProgress.start();
$.post('$this->ajax', window['expandpost'+targ.replace('#', '')], function(d){
    NProgress.done();
    if (d) 
       \$c.html(d);
    else 
       \$c.html('{$this->noDataTip()}');
       
       id.collapse('show');
    setTimeout(function(){
        t.button('reset');
    },200)
});
EOF;
        }

        // 保存post数据
        Admin::script("window['expandpost{$id}']=$post;");

        $this->script('expand', <<<SCRIPT
$('.grid-expand').click(function(){
    var t = $(this),
        i = t.find('i'),
        id = $(t.data('target'));
    i.toggleClass('fa-caret-right');
    i.toggleClass('fa-caret-down');
    if (t.attr('ajax')) {
        id.collapse('toggle');
        return; 
    }
    {$script}
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
     * @param string $api
     * @param array  $post
     * @return $this
     */
    public function ajax($api, array $post = [])
    {
        $this->ajax = &$api;
        $this->post = &$post;
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
            $this->content = $content($this->items);
        } else {
            $this->content = $content;
        }

        return $this;
    }
}
