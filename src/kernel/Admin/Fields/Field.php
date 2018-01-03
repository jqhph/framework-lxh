<?php

namespace Lxh\Admin\Fields;

use Lxh\Contracts\Support\Renderable;

class Field implements Renderable
{
    /**
     * 字段名称
     *
     * @var string
     */
    protected $name;

    /**
     *
     * @var mixed
     */
    protected $value;

    /**
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * @var array
     */
    protected $options = [];

    /**
     * View for field to render.
     *
     * @var string
     */
    protected $view;

    /**
     * @var string
     */
    protected $label = '';

    public function __construct($name, $value, $options = [])
    {
        $this->value = &$value;

        $this->options = array_merge($this->options, (array)$options);
    }

    public function label($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @param null $name
     * @return $this|string
     */
    public function name($name = null)
    {
        if ($name === null) {
            return $this->name;
        }
        $this->name = $name;
        return $this;
    }

    protected function getElementSelector()
    {
        if ($id = $this->getAttribute('id')) {
            return $id;
        }

        return "[data-name=\"{$this->name}\"]";
    }

    protected function buildSelectorAttribute()
    {
        if ($id = $this->getAttribute('id')) {
            return $this;
        }

        return $this->attribute('data-name', $this->name);
    }

    /**
     * Get or set option for grid.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return $this|mixed
     */
    public function option($key, $value = null)
    {
        $isArray = is_array($key);
        if (is_null($value) && !$isArray) {
            return $this->options[$key];
        }

        if ($isArray) {
            $this->options = array_merge($this->options, $key);
        } else {
            $this->options[$key] = $value;
        }

        return $this;
    }

    /**
     * Add html attributes to elements.
     *
     * @param array|string $attribute
     * @param mixed        $value
     *
     * @return $this
     */
    public function attribute($attribute, $value = null)
    {
        if (is_array($attribute)) {
            $this->attributes = array_merge($this->attributes, $attribute);
        } else {
            $this->attributes[$attribute] = (string) $value;
        }

        return $this;
    }

    public function getAttribute($k, $def = null)
    {
        return get_value($this->attributes, $k, $def);
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

    public function render()
    {
        $this->buildSelectorAttribute();

        if ($this->view) {
            return view($this->view, array_merge([
                'value' => $this->value,
                'attributes' => $this->formatAttributes(),
            ], $this->options));
        }
        return $this->value;
    }

}
