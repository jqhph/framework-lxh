<?php

namespace Lxh\Admin\Fields;

use Lxh\Admin\Data\Items;
use Lxh\Admin\Grid;
use Lxh\Admin\Http\Controllers\Admin;
use Lxh\Admin\Table\Table;
use Lxh\Admin\Table\Tr;
use Lxh\Contracts\Support\Renderable;
use Lxh\Helper\Util;

/**
 * @method $this class($class)
 */
class Field implements Renderable
{
    /**
     * @var array
     */
    protected static $loadedScripts = [];

    /**
     * @var array
     */
    protected static $js = [];

    /**
     * @var array
     */
    protected static $css = [];

    /**
     * @var Table
     */
    protected $table;

    /**
     * @var Items
     */
    protected $items;

    /**
     * @var \Closure
     */
    protected $then;

    /**
     *
     * @var mixed
     */
    protected $value;

    /**
     * @var string
     */
    protected $name;

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

    /**
     * @var string
     */
    protected $containerId = '';

    /**
     * Field constructor.
     *
     * @param null $name
     * @param null $value
     * @param array $options
     */
    public function __construct($name = null, $value = null, $options = [])
    {
        $this->name = $name;
        $this->value = &$value;
        if ($options) {
            $this->options = (array)$options;
        }
    }

    /**
     * @return mixed
     */
    protected function getModelId()
    {
        return $this->items->get(\Lxh\Admin\Admin::id());
    }

    /**
     * @param $label
     * @return $this|string
     */
    public function label($label = null)
    {
        if ($label !== null) {
            $this->label = &$label;
            return $this;
        }
        return $this->label ?: ($this->value ?: $this->name);
    }

    /**
     * @param Table $table
     * @return $this
     */
    public function setTable(Table $table)
    {
        $this->table = $table;
        return $this;
    }

    /**
     * 设置行数据对象
     *
     * @param Items $items
     * @return $this
     */
    public function setItems(Items $items)
    {
        $this->items = $items;
        return $this;
    }

    /**
     * 获取行数据对象
     *
     * @return Items
     */
    public function items()
    {
        return $this->items;
    }

    /**
     * 获取行数
     *
     * @return int
     */
    public function line()
    {
        return $this->items->offset() + 1;
    }

    /**
     * 设置js
     *
     * @param string $unique
     * @param string $script
     * @return $this
     */
    public function script($unique, $script)
    {
        if (empty(static::$loadedScripts[$unique])) {
            \Lxh\Admin\Admin::script($script);

            static::$loadedScripts[$unique] = 1;
        }

        return $this;
    }

    /**
     * 获取当前行其他字段
     *
     * @param string $key
     */
    public function item($key)
    {
        return $this->items->column($key);
    }

    /**
     * @return string|$this
     */
    public function name($name = null)
    {
        if ($name !== null) {
            $this->name = &$name;
            return $this;
        }

        return $this->name;
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
     * 当字段调用render方法时会触发此回调函数
     *
     * @param \Closure $then
     * @return $this
     */
    public function then(\Closure $then)
    {
        $this->then = $then;

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
     * @return string
     */
    public function getElementSelector()
    {
        if ($id = $this->getAttribute('id')) {
            return '#' . $id;
        }

       $this->buildSelectorAttribute();

        return '#' . $this->getAttribute('id');
    }

    /**
     * @param null $value
     * @return $this|mixed
     */
    public function value($value = null)
    {
        if ($value !== null) {
            $this->value = &$value;
            return $this;
        }

        return $this->value;

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
     * @return $this
     */
    public function buildSelectorAttribute()
    {
        if ($id = $this->getAttribute('id')) {
            return $this;
        }

        return $this->generateId();
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
            return isset($this->options[$key]) ? $this->options[$key] : null;
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

    /**
     * @param $k
     * @param null $def
     * @return mixed
     */
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

    public static function getAssets()
    {
        return ['js' => &static::$js, 'css' => &static::$css];
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
