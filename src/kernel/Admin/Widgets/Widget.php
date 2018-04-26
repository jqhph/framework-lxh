<?php

namespace Lxh\Admin\Widgets;

use Lxh\Contracts\Support\Renderable;
use Lxh\Helper\Util;
use Lxh\Support\Fluent;

/**
 * @method \Lxh\Admin\Widgets\Widget class($class)
 */
abstract class Widget extends Fluent implements Renderable
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
            if (is_numeric($key)) {
                $key = $value;
            }
            if (!is_null($value)) {
                $html[] = $key.'="'.htmlentities($value, ENT_QUOTES, 'UTF-8').'"';
            } else {
                $html[] = $key;
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
     * @param $k
     * @param null $def
     * @return mixed
     */
    public function getAttribute($k, $def = null)
    {
        return getvalue($this->attributes, $k, $def);
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
     * @return string
     */
    public function getIdElementSelector()
    {
        if ($id = $this->getAttribute('id')) {
            return '#' . $id;
        }

        $this->generateId();

        return '#' . $this->getAttribute('id');
    }

    /**
     * 随机生成id
     *
     * @return $this
     */
    public function generateId()
    {
        $this->attribute('id', Util::randomString());

        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->getAttribute('id');
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
