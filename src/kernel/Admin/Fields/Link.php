<?php

namespace Lxh\Admin\Fields;

use Lxh\Contracts\Support\Renderable;

class Link extends Field
{
    /**
     * @var string
     */
    protected $url;

    /**
     * 是否使用ajax 弹窗展示数据
     *
     * @var bool
     */
    protected $useModal = false;

    /**
     * @var string
     */
    protected $icon = '';

    /**
     * @var array
     */
    protected $options = [
        'color' => 'primary',
        'useTab' => true,
        'id' => 'button',
        'format' => '',
        'formatKey' => '',
    ];

    public function render()
    {
        if (($this->value === '' || $this->value === null) && empty($this->label)) {
            return false;
        }

        if (! $this->url && ($format = $this->option('format'))) {
            $replace = $this->options['formatKey'] ? $this->item($this->options['formatKey']) : $this->value;

            if (! $replace) return '';

            $this->url = str_replace('{value}', $replace, $format);
        }

        $this->class('tag-cloud tag-link');

        if ($url = $this->url()) {
            $this->buildSelectorAttribute();
            $this->attribute('onclick', $url);
        }
        $icon = '';
        if ($this->icon) {
            $icon = "<i class='{$this->icon}'></i> ";
        }

        return "<span {$this->formatAttributes()}>{$icon} {$this->label()}</span> ";
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
     * @param $string
     * @param $key
     * @return $this
     */
    public function format($format, $key = null)
    {
        if ($key) {
            $this->option('formatKey', $key);
        }

        return $this->option('format', $format);
    }

    /**
     * 使用弹窗展示数据
     *
     * @return $this
     */
    public function useAjaxModal()
    {
        $this->useModal = true;

        return $this->class('ajax-modal');
    }

    /**
     * 设置ajax modal弹窗标题
     *
     * @param $title
     * @return $this
     */
    public function title($title)
    {
        return $this->attribute('modal-title', $title);
    }

    /**
     * ajax modal 设置dataid，用于缓存从服务器抓取的数据，无需每次重复抓取
     *
     * @param $dataId
     * @return $this
     */
    public function dataId($dataId)
    {
        return $this->attribute('modal-data-id', $dataId);
    }

    /**
     * 设置普通链接或ajax modal弹窗取数据接口
     *
     * @param null $url
     * @return $this|string
     */
    public function url($url = null)
    {
        if ($url === null) {
            return $this->defaultUrl();
        }

        if ($this->useModal) {
            // 使用ajax modal
            return $this->attribute('modal-url', $url);
        }

        return $this->defaultUrl($url);
    }

    protected function defaultUrl($url = null)
    {
        if ($url) {
            $this->url = &$url;
            return $this;
        }

        if ($this->option('useTab') && $this->url) {
            $name = str_replace('/', '-', $this->url);
            return "open_tab('{$name}', '{$this->url}', '{$this->label()}')";
        }
        return $this->url;
    }

}
