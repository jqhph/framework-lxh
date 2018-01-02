<?php

namespace Lxh\Admin\Widgets;

use Lxh\Support\Fluent;

/**
 * @method static class($class)
 */
abstract class Widget extends Fluent
{
    /**
     * @var string
     */
    protected $view;

    /**
     * @return mixed
     */
    abstract public function render();

    /**
     * Set view of widget.
     *
     * @param string $view
     */
    public function view($view)
    {
        $this->view = $view;
    }

    /**
     * Build an HTML attribute string from an array.
     *
     * @return string
     */
    public function formatAttributes()
    {
        $html = [];
        foreach ($this->attributes as $key => &$value) {
            $element = $this->attributeElement($key, $value);
            if (!is_null($element)) {
                $html[] = $element;
            }
        }

        return count($html) > 0 ? ' '.implode(' ', $html) : '';
    }

    /**
     * 设置样式
     *
     * @param $style
     * @return $this
     */
    public function setStyle($style)
    {
        $this->attributes['style'] = &$style;
        return $this;
    }

    /**
     * @param $id
     * @return $this
     */
    public function id($id)
    {
        $this->attributes['id'] = $id;

        return $this;
    }

    /**
     * 设置css class
     *
     * @param $class
     * @return $this
     */
    public function setClass($class)
    {
        $this->attributes['class'] = &$class;
        return $this;
    }

    /**
     * 追加样式
     *
     * @param $style
     * @return $this
     */
    public function style($style)
    {
        if (isset($this->attributes['style'])) {
            $this->attributes['style'] = "{$this->attributes['style']};$style";
        } else {
            $this->attributes['style'] = &$style;
        }
        return $this;
    }

    /**
     * @param $k
     * @param $v
     * @return $this
     */
    public function attribute($k, $v)
    {
        $this->attributes[$k] = &$v;

        return $this;
    }

    /**
     * Build a single attribute element.
     *
     * @param string $key
     * @param string $value
     *
     * @return string
     */
    protected function attributeElement($key, $value)
    {
        if (is_numeric($key)) {
            $key = $value;
        }
        if (!is_null($value)) {
            return $key.'="'.htmlentities($value, ENT_QUOTES, 'UTF-8').'"';
        }
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * Handle dynamic calls to the container to set attributes.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return $this
     */
    public function __call($method, $parameters)
    {
        $p = count($parameters) > 0 ? $parameters[0] : true;
        if ($method == 'class') {
            if (isset($this->attributes[$method])) {
                $this->attributes[$method] = "{$this->attributes[$method]} $p";
            } else {
                $this->attributes[$method] = &$p;
            }
            return $this;
        }
        $this->attributes[$method] = &$p;

        return $this;
    }
}
