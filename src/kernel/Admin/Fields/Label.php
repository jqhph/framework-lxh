<?php

namespace Lxh\Admin\Fields;

class Label extends Button
{
    /**
     * @var array
     */
    protected static $colors = [];

    /**
     * @var array
     */
    protected $allowColors = [
        'success', 'danger', 'primary', 'default', 'pink', 'purple', 'inverse', 'warning', 'info'
    ];

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
        $this->class("$this->effect label");

        if ($this->url) {
            $this->setupUrlScript();
        }
        return $this->buildTags();
    }

    protected function buildTags()
    {
        $class = $this->getAttribute('class');
        // 是否使用随机颜色
        $useRandomColor = $this->option('useRandomColor');

        $tags = '';
        $counter = 0;
        foreach ((array)$this->label() as &$value) {
            if (!$value) continue;
            $color = $this->getColor($useRandomColor, $counter);

            $this->setClass("$class label-$color");

            $tags .= "<span {$this->formatAttributes()}>{$value}</span> ";

            $counter++;
        }

        return $tags;
    }

    protected function getColor($useRandomColor, &$counter)
    {
        if (! $useRandomColor) {
            return $this->options['color'];
        }
        if (isset(static::$colors[$counter])) {
            $color = static::$colors[$counter];
        } else {
            $color = static::$colors[0];
            $counter ++;
        }

        return $color;
    }

    /**
     * 使用随机打乱颜色
     *
     * @return $this|mixed
     */
    public function useRandomColor()
    {
        if (! static::$colors) {
            shuffle($this->allowColors);
            static::$colors = $this->allowColors;
        }

        return $this->option('useRandomColor', true);
    }

}
