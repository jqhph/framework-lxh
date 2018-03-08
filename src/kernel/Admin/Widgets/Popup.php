<?php

namespace Lxh\Admin\Widgets;

use Lxh\Admin\Admin;
use Lxh\Contracts\Support\Renderable;
use Lxh\Helper\Util;

/**
 * 注意：弹窗是在父窗口弹出
 *      所以当父窗口没有弹窗内容所用到的css时，内容css渲染将无效
 *      此时请使用iframe弹窗或者Modal代替
 *
 */
class Popup
{
    protected $id;
    protected $method = 'open';
    protected $area = [];
    protected $options = [
        'title' => '',
        'content' => '',
        'type' => 1,
        'shadeClose' => true,
    ];

    public function __construct($title = '', $content = '')
    {
        $this->id = 'p'.Util::randomString(6);

        $this->options['title']   = &$title;
        $this->options['content'] = &$content;
    }

    public function method($method)
    {
        $this->method = $method;

        return $this;
    }

    public function area($width, $height = 'auto')
    {
        $this->options['area'] = [$width, $height];
        return $this;
    }

    public function iframe($url)
    {
        $this->options['content'] = $url;
        $this->options['type']    = 2;

        return $this;
    }

    public function option($k, $v = null)
    {
        if (is_array($k)) {
            $this->options = array_merge($this->options, $k);
        } else {
            $this->options[$k] = &$v;
        }

        return $this;
    }

    public function type($type)
    {
        $this->options['type'] = $type;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getScript()
    {
        if ($this->options['content'] instanceof Renderable) {
            $this->options['content'] = $this->options['content']->render();
        }

        Admin::hidden("<div id='{$this->id}'>{$this->options['content']}</div>");

        $this->options['content'] = '';
        $opts = json_encode($this->options);

        return "function {$this->id}(){
            var opts = $opts;
            opts.content = $('#{$this->id}').html();
            layer.{$this->method}(opts);
        };";
    }

}
