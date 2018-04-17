<?php

namespace Lxh\Admin\Fields;

use Lxh\Admin\Admin;
use Lxh\Contracts\Support\Renderable;
use Lxh\Helper\Util;

class Button extends Field
{
    protected static $setupUrlScripts = [];

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $icon;

    /**
     * @var array
     */
    protected $options = [
        'color' => 'primary',
        'id' => 'button',
    ];

    protected $popup = false;

    protected $title;

    protected $area = ['width' => '65%', 'height' => '660px'];

    /**
     * @var string
     */
    protected $effect = 'waves-effect';

    public function __construct($label = null, $url = null)
    {
        $this->label = &$label;
        $this->url   = &$url;
    }

    public function sm()
    {
        return $this->class('btn-sm');
    }

    public function xs()
    {
        return $this->class('btn-xs');
    }

    /**
     * @param $color
     * @return $this|mixed
     */
    public function color($color)
    {
        return $this->option('color', $color);
    }

    /**
     * 添加图标
     *
     * @return $this
     */
    public function icon($icon)
    {
        $this->icon = &$icon;
        return $this;
    }

    /**
     * @return $this
     */
    public function disableEffect()
    {
        $this->effect = '';
        return $this;
    }

    public function render()
    {
        $this->class("$this->effect btn btn-{$this->option('color')}");

        if ($this->url) {
            $this->setupUrlScript();
        }

        $icon = '';
        if ($this->icon) {
            $icon = "&nbsp; <i class='{$this->icon}'></i>";
        }

        return "<button {$this->formatAttributes()}>{$this->label()}{$icon}</button>";
    }

    protected function setupUrlScript()
    {
        if (! $this->popup) {
            $this->buildSelectorAttribute();

            $name = str_replace('/', '-', $this->url);

            $this->attribute('onclick', "open_tab('{$name}', '{$this->url}', '{$this->label()}')");

            return;
        }

        // 弹窗模式
        $class = 'btn-popup-'.ord($this->name);
        $this->class($class);
        $this->attribute('data-url', $this->url);
        $this->attribute('data-title', $this->title);
        if (!empty(static::$setupUrlScripts[$this->name])) {
            return;
        }
        static::$setupUrlScripts[$this->name] = true;

        $script = "var url = $(this).data('url'), title = $(this).data('title');
        layer.open({
          type: 2,
          title: title,
          shadeClose: true,
          shade: false,
          area: ['{$this->area['width']}', '{$this->area['height']}'],
          content: url,
        });";

        $this->on('click', $script, '.'.$class);
    }

    /**
     * 绑定js事件
     *
     * @param $event
     * @param $callback
     * @param $selector
     */
    public function on($event, $callback, $selector = '')
    {
        if (! $selector) {
            $selector = $this->getElementSelector();
        }

        Admin::script(<<<EOT
$('{$selector}').on('$event',function(e) {{$callback}});
EOT
        );
    }

    /**
     * @param string $url
     * @return $this
     */
    public function url($url = null)
    {
        $this->url = &$url;
        return $this;
    }

    /**
     * 使用弹窗模式
     *
     * @return $this
     */
    public function popup()
    {
        $this->popup = true;
        return $this;
    }

    /**
     * 弹窗标题
     *
     * @param $title
     * @return $this
     */
    public function title($title)
    {
        $this->title = &$title;
        return $this;
    }

    /**
     * 弹窗宽高设置
     *
     * @param $width
     * @param string $height
     * @return $this
     */
    public function area($width, $height = '660px')
    {
        $this->area = [
            'width' => $width, 'height' => $height
        ];
        return $this;
    }

}
