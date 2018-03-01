<?php

namespace Lxh\Admin\Widgets;

use Lxh\Admin\Admin;
use Lxh\Admin\Form\Field;
use Lxh\Admin\Grid;
use Lxh\Contracts\Support\Arrayable;
use Lxh\Contracts\Support\Renderable;
use Lxh\Exceptions\InvalidArgumentException;
use Lxh\Support\Arr;

/**
 * Class Form.
 *
 * @method Field\Text           text($name, $label = '')
 * @method Field\Password       password($name, $label = '')
 * @method Field\Checkbox       checkbox($name, $label = '')
 * @method Field\Switcher       switch($name, $label = '')
 * @method Field\Radio          radio($name, $label = '')
 * @method Field\Select         select($name, $label = '')
 * @method Field\SelectTree     selectTree($name, $label = '')
 * @method Field\MultipleSelect multipleSelect($name, $label = '')
 * @method Field\Textarea       textarea($name, $label = '')
 * @method Field\Hidden         hidden($name, $label = '')
 * @method Field\Id             id($name, $label = '')
 * @method Field\Ip             ip($name, $label = '')
 * @method Field\Url            url($name, $label = '')
 * @method Field\Color          color($name, $label = '')
 * @method Field\Email          email($name, $label = '')
 * @method Field\Mobile         mobile($name, $label = '')
 * @method Field\Slider         slider($name, $label = '')
 * @method Field\Map            map($name, $latitude = '', $longitude = '')
 * @method Field\Editor         editor($name, $label = '')
 * @method Field\File           file($name, $label = '')
 * @method Field\Image          image($name, $label = '')
 * @method Field\Date           date($name, $label = '')
 * @method Field\Datetime       datetime($name, $label = '')
 * @method Field\Time           time($name, $label = '')
 * @method Field\DateRange      dateRange($start, $end, $label = '')
 * @method Field\DateTimeRange  dateTimeRange($start, $end, $label = '')
 * @method Field\TimeRange      timeRange($start, $end, $label = '')
 * @method Field\Month          month($name, $label = '')
 * @method Field\Year           year($name, $label = '')
 * @method Field\Number         number($name, $label = '')
 * @method Field\Currency       currency($name, $label = '')
 * @method Field\Display        display($name, $label = '')
 * @method Field\Rate           rate($name, $label = '')
 * @method Field\Divide         divide()
 * @method Field\Decimal        decimal($column, $label = '')
 * @method Field\Html           html($column = '', $label = '')
 * @method Field\Tags           tags($column, $label = '')
 * @method Field\Icon           icon($column, $label = '')
 * @method Field\TableCheckbox  tableCheckbox($column, $label = '')
 */
class Form implements Renderable
{
    /**
     * 字段id
     *
     * @var mixed
     */
    protected $id;

    /**
     * @var string
     */
    protected $name = '';
    /**
     * @var Field[]
     */
    protected $fields = [];

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * 需要异步加载的js
     * @var array
     */
    protected $asyncJs = [];

    /**
     * @var array
     */
    protected $options = [
        'enableSubmit' => true,
        'enableReset'  => true,
        'editScript' => true,
    ];

    /**
     * Form constructor.
     *
     * @param array $data
     */
    public function __construct($data = [])
    {
        $this->name = __CONTROLLER__;

        if ($data instanceof Arrayable) {
            $data = $data->toArray();
        }

        if (!empty($data)) {
            $this->data = $data;
        }

        $this->initFormAttributes();
    }

    /**
     * @param $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * 查询数据
     *
     * @throws InvalidArgumentException
     */
    public function find()
    {
        if (! $this->id) {
            throw new InvalidArgumentException('Miss id');
        }

        if (! $this->data) {
            $this->data = model(Admin::model())->setId($this->id)->find();
        }

        return $this;
    }

    /**
     * Add or get options.
     *
     * @param array $options
     *
     * @return array|null
     */
    public function options($options = [])
    {
        if (empty($options)) {
            return $this->options;
        }

        $this->options = array_merge($this->options, $options);
    }

    /**
     * Get or set option.
     *
     * @param string $option
     * @param mixed  $value
     *
     * @return $this
     */
    public function option($option, $value = null)
    {
        if (func_num_args() == 1) {
            return get_value($this->options, $option);
        }

        $this->options[$option] = $value;

        return $this;
    }

    /**
     * Initialize the form attributes.
     */
    protected function initFormAttributes()
    {
        $this->attributes = [
            'method'         => 'POST',
            'action'         => '',
            'class'          => 'form-horizontal ' . $this->getElementClass(),
            'accept-charset' => 'UTF-8',
            'pjax-container' => true,
        ];
    }

    /**
     * 获取元素
     *
     * @return string
     */
    public function getElementClass()
    {
        return $this->name . '-form';
    }

    /**
     * Action uri of the form.
     *
     * @param string $action
     *
     * @return $this
     */
    public function action($action)
    {
        return $this->attribute('action', $action);
    }

    /**
     * Method of the form.
     *
     * @param string $method
     *
     * @return $this
     */
    public function method($method = 'POST')
    {
        return $this->attribute('method', strtoupper($method));
    }

    /**
     * Add form attributes.
     *
     * @param string|array $attr
     * @param string       $value
     *
     * @return $this
     */
    public function attribute($attr, $value = '')
    {
        if (is_array($attr)) {
            foreach ($attr as $key => &$value) {
                $this->attribute($key, $value);
            }
        } else {
            $this->attributes[$attr] = &$value;
        }

        return $this;
    }

    /**
     * 设置获取模块名
     *
     * @param string $name
     * @return static | string
     */
    public function name($name = null)
    {
        if ($name) {
            $this->name = $name;
            return $this;
        }
        return $this->name;
    }

    /**
     * 使用编辑模块js
     *
     * @param string $js 需要异步加载的js
     * @return static
     */
    public function useScript($js)
    {
        return $this->async($js);
    }

    /**
     * 禁用公共编辑js
     *
     * @return $this
     */
    public function disableEditScript()
    {
        $this->options['editScript'] = false;
        return $this;
    }

    /**
     * Disable Pjax.
     *
     * @return $this
     */
    public function disablePjax()
    {
        Arr::forget($this->attributes, Grid::getPjaxContainerId());

        return $this;
    }

    /**
     * Set field and label width in current form.
     *
     * @param int $fieldWidth
     * @param int $labelWidth
     *
     * @return $this
     */
    public function setWidth($fieldWidth = 8, $labelWidth = 2)
    {
        collect($this->fields)->each(function ($field) use ($fieldWidth, $labelWidth) {
            /* @var Field $field  */
            $field->setWidth($fieldWidth, $labelWidth);
        });

        return $this;
    }

    /**
     * Find field class with given name.
     *
     * @param string $method
     *
     * @return bool|string
     */
    public static function findFieldClass($method)
    {
        $class = get_value(\Lxh\Admin\Form::$availableFields, $method);

        if (class_exists($class)) {
            return $class;
        }

        return false;
    }

    /**
     * 异步加载js
     *
     * @param $js
     * @return $this
     */
    public function async($js)
    {
        $this->asyncJs[] = &$js;

        return $this;
    }

    /**
     * Add a form field to form.
     *
     * @param Field $field
     *
     * @return $this
     */
    protected function pushField(Field $field, $className = null)
    {
        array_push($this->fields, $field);

        $field->setForm($this);

        $className = $className ?: get_class($field);

        Admin::addAssetsFieldClass($className);
        Admin::addScriptClass($className);

        return $this;
    }

    /**
     * Get variables for render form.
     *
     * @return array
     */
    protected function getVariables()
    {
        if ($this->id) {
            $this->find();
        }

        if ($this->options['editScript']) {
            $this->async('@lxh/js/public-detail');
        }

        foreach ($this->fields as $field) {
            $field->fill($this->data);
            $field->callAttaching();
        }

        return [
            'fields'      => $this->fields,
            'attributes'  => $this->formatAttribute(),
            'asyncJs'     => &$this->asyncJs,
            'formOptions' => &$this->options,
            'id'          => $this->id,
        ];
    }

    /**
     * Disable form submit.
     *
     * @return $this
     */
    public function disableSubmit()
    {
        $this->options(['enableSubmit' => false]);

        return $this;
    }

    /**
     * Disable form reset.
     *
     * @return $this
     */
    public function disableReset()
    {
        $this->options(['enableReset' => false]);

        return $this;
    }

    /**
     * Format form attributes form array to html.
     *
     * @param array $attributes
     *
     * @return string
     */
    public function formatAttribute($attributes = [])
    {
        $attributes = $attributes ?: $this->attributes;

        if ($this->hasFile()) {
            $attributes['enctype'] = 'multipart/form-data';
        }

        $html = [];
        foreach ($attributes as $key => $val) {
            $html[] = "$key=\"$val\"";
        }

        return implode(' ', $html) ?: '';
    }

    /**
     * Determine if form fields has files.
     *
     * @return bool
     */
    public function hasFile()
    {
        foreach ($this->fields as $field) {
            if ($field instanceof Field\File) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate a Field object and add to form builder if Field exists.
     *
     * @param string $method
     * @param array  $arguments
     *
     * @return Field|null
     */
    public function __call($method, $arguments)
    {
        if ($className = static::findFieldClass($method)) {
            $name = get_value($arguments, 0, '');

            $element = new $className($name, array_slice($arguments, 1));

            $this->pushField($element);

            return $element;
        }
    }

    /**
     * Render the form.
     *
     * @return string
     */
    public function render()
    {
        return view('admin::widget.form', $this->getVariables())->render();
    }

    /**
     * Output as string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }
}
