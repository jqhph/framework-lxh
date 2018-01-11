<?php

namespace Lxh\Admin\Fields;

use Lxh\Contracts\Support\Renderable;

class Link extends Tag
{
    /**
     * 是否使用ajax 弹窗展示数据
     *
     * @var bool
     */
    protected $useModal = false;

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
        $this->style('margin-left:0;margin-right:0;padding-top:0');

        $this->callThen();

        if (! $this->url && ($format = $this->option('format'))) {
            $replace = $this->options['formatKey'] ? $this->tr->row($this->options['formatKey']) : $this->value;

            $this->url = str_replace('{value}', $replace, $format);
        }

        $html = parent::render(); // TODO: Change the autogenerated stub

        // 列表展示时，由于是使用同一实例渲染多行字段，需要重置url参数。否则多行字段用的将是同一个链接
        $this->url = '';

        return $html;
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
            return parent::url();
        }

        if ($this->useModal) {
            // 使用ajax modal
            return $this->attribute('modal-url', $url);
        }

        return parent::url($url);
    }
    
}
