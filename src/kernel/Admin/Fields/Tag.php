<?php

namespace Lxh\Admin\Fields;

use Lxh\Contracts\Support\Renderable;

class Tag extends Button
{
    /**
     * @var string
     */
    protected $size = '';

    public function __construct($name = null, $value = null, $options = [])
    {
        $this->name = &$name;
        $this->value = &$value;
        if ($options) {
            $this->options = array_merge($this->options, (array)$options);
        }
    }

    public function render()
    {
        $this->class("$this->effect tag-cloud $this->size");

        if ($url = $this->url()) {
            $this->buildSelectorAttribute();
            $this->attribute('onclick', $url);
        }

        return $this->buildTags();
    }

    /**
     * @return $this
     */
    public function small()
    {
        $this->size = 'tag-sm';
        return $this;
    }

    /**
     * @return $this
     */
    public function middle()
    {
        $this->size = 'tag-md';
        return $this;
    }

    /**
     * @return $this
     */
    public function large()
    {
        $this->size = 'tag-lg';
        return $this;
    }

    protected function buildTags()
    {
        $icon = '';
        if ($this->icon) {
            $icon = "<i class='{$this->icon}'></i> ";
        }

        $class = $this->getAttribute('class');

        $tags = '';
        $counter = 0;
        $attributes = $this->formatAttributes();
        foreach ((array)$this->label() as &$value) {
            $this->setClass("$class ");

            $tags .= "<span {$attributes}>{$icon} {$value}</span> ";

            $counter++;
        }

        return $tags;
    }

    
    /**
     * 添加图标
     */
    public function icon($icon)
    {
        $this->icon = &$icon;
        return $this;
    }

}
