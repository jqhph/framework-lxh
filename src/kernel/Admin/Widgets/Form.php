<?php

namespace Lxh\Admin\Widgets;

use Lxh\Admin\Admin;
use Lxh\Admin\Form\Field;
use Lxh\Admin\Grid;
use Lxh\Admin\Layout\Content;
use Lxh\Contracts\Support\Arrayable;
use Lxh\Contracts\Support\Renderable;
use Lxh\Exceptions\InvalidArgumentException;
use Lxh\Support\Arr;

/**
 * Class Form.
 *
 * @method \Lxh\Admin\Form\Field\Text           text($name, $label = '')
 * @method \Lxh\Admin\Form\Field\Password       password($name, $label = '')
 * @method \Lxh\Admin\Form\Field\Checkbox       checkbox($name, $label = '')
 * @method \Lxh\Admin\Form\Field\Switcher       switch($name, $label = '')
 * @method \Lxh\Admin\Form\Field\Radio          radio($name, $label = '')
 * @method \Lxh\Admin\Form\Field\Select         select($name, $label = '')
 * @method \Lxh\Admin\Form\Field\SelectTree     selectTree($name, $label = '')
 * @method \Lxh\Admin\Form\Field\MultipleSelect multipleSelect($name, $label = '')
 * @method \Lxh\Admin\Form\Field\Textarea       textarea($name, $label = '')
 * @method \Lxh\Admin\Form\Field\Hidden         hidden($name, $label = '')
 * @method \Lxh\Admin\Form\Field\Id             id($name, $label = '')
 * @method \Lxh\Admin\Form\Field\Ip             ip($name, $label = '')
 * @method \Lxh\Admin\Form\Field\Url            url($name, $label = '')
 * @method \Lxh\Admin\Form\Field\Color          color($name, $label = '')
 * @method \Lxh\Admin\Form\Field\Email          email($name, $label = '')
 * @method \Lxh\Admin\Form\Field\Mobile         mobile($name, $label = '')
 * @method \Lxh\Admin\Form\Field\Slider         slider($name, $label = '')
 * @method \Lxh\Admin\Form\Field\Map            map($name, $latitude = '', $longitude = '')
 * @method \Lxh\Admin\Form\Field\Editor         editor($name, $label = '')
 * @method \Lxh\Admin\Form\Field\File           file($name, $label = '')
 * @method \Lxh\Admin\Form\Field\MultipleFile   multipleFile($name, $label = '')
 * @method \Lxh\Admin\Form\Field\Image          image($name, $label = '')
 * @method \Lxh\Admin\Form\Field\MultipleImage  multipleImage($name, $label = '')
 * @method \Lxh\Admin\Form\Field\Date           date($name, $label = '')
 * @method \Lxh\Admin\Form\Field\Datetime       datetime($name, $label = '')
 * @method \Lxh\Admin\Form\Field\Time           time($name, $label = '')
 * @method \Lxh\Admin\Form\Field\DateRange      dateRange($name, $start = '', $end = '')
 * @method \Lxh\Admin\Form\Field\DateTimeRange  dateTimeRange($name, $start = '', $end = '')
 * @method \Lxh\Admin\Form\Field\TimeRange      timeRange($name, $start = '', $end = '')
 * @method \Lxh\Admin\Form\Field\Month          month($name, $label = '')
 * @method \Lxh\Admin\Form\Field\Year           year($name, $label = '')
 * @method \Lxh\Admin\Form\Field\Number         number($name, $label = '')
 * @method \Lxh\Admin\Form\Field\Currency       currency($name, $label = '')
 * @method \Lxh\Admin\Form\Field\Rate           rate($name, $label = '')
 * @method \Lxh\Admin\Form\Field\Divide         divide()
 * @method \Lxh\Admin\Form\Field\Decimal        decimal($column, $label = '')
 * @method \Lxh\Admin\Form\Field\Html           html($column = '', $label = '')
 * @method \Lxh\Admin\Form\Field\Icon           icon($column, $label = '')
 * @method \Lxh\Admin\Form\Field\TableCheckbox  tableCheckbox($column, $label = '')
 */
class Form implements Renderable
{
    /**
     * Available fields.
     *
     * @var array
     */
    public static $availableFields = [
        'button'         => Field\Button::class,
        'checkbox'       => Field\Checkbox::class,
        'color'          => Field\Color::class,
        'currency'       => Field\Currency::class,
        'date'           => Field\Date::class,
        'dateRange'      => Field\DateRange::class,
        'datetime'       => Field\Datetime::class,
        'dateTimeRange'  => Field\DatetimeRange::class,
        'decimal'        => Field\Decimal::class,
        'divider'        => Field\Divide::class,
        'divide'         => Field\Divide::class,
        'embeds'         => Field\Embeds::class,
        'editor'         => Field\Editor::class,
        'email'          => Field\Email::class,
        'file'           => Field\File::class,
        'hidden'         => Field\Hidden::class,
        'id'             => Field\Id::class,
        'image'          => Field\Image::class,
        'ip'             => Field\Ip::class,
        'map'            => Field\Map::class,
        'mobile'         => Field\Mobile::class,
        'month'          => Field\Month::class,
        'multipleSelect' => Field\MultipleSelect::class,
        'number'         => Field\Number::class,
        'password'       => Field\Password::class,
        'radio'          => Field\Radio::class,
        'rate'           => Field\Rate::class,
        'select'         => Field\Select::class,
        'selectTree'     => Field\SelectTree::class,
        'slider'         => Field\Slider::class,
        'text'           => Field\Text::class,
        'textarea'       => Field\Textarea::class,
        'time'           => Field\Time::class,
        'timeRange'      => Field\TimeRange::class,
        'url'            => Field\Url::class,
        'year'           => Field\Year::class,
        'html'           => Field\Html::class,
        'icon'           => Field\Icon::class,
        'multipleFile'   => Field\MultipleFile::class,
        'multipleImage'  => Field\MultipleImage::class,
        'tableCheckbox'  => Field\TableCheckbox::class,
        'switch'         => Field\Switcher::class,
    ];

    /**
     *
     * @var Content
     */
    protected $content;

    /**
     * 表单呈多块布局
     *
     * @var bool
     */
    protected $multiples = false;

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
     * @var array
     */
    protected $options = [
        'enableSubmit'   => true,
        'enableReset'    => true,
        'submitBtnLabel' => '',
        'resetBtnLabel'  => '',
        'editScript'     => true,
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

    public function setContent(Content $content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * 查询数据
     *
     * @return array
     * @throws InvalidArgumentException
     */
    public function find()
    {
        if (! $this->data && $this->id) {
            $this->data = model(Admin::model())->setId($this->id)->find();
        }

        return $this->data ;
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
     * 允许表单拆分成多块布局
     *
     * @return $this
     */
    public function multiples()
    {
        $this->multiples = true;

        return $this;
    }

    /**
     * 创建子表单
     *
     * @return $this
     */
    public function create()
    {
        $this->multiples = true;

        $form = new static($this->find());

        $form->disableReset();
        $form->disableSubmit();

        return $form;
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
            return getvalue($this->options, $option);
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
     * 获取css类
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
        return Admin::js($js);
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
            Admin::js('@lxh/js/public-detail');
        }

        if ($this->data) {
            foreach ($this->fields as $field) {
                $field->fill($this->data);
                $field->callAttaching();
            }
        }

        return [
            'fields'      => $this->fields,
            'attributes'  => $this->formatAttribute(),
            'formOptions' => &$this->options,
            'id'          => $this->id,
            'content'     => $this->content,
            'multiples'   => $this->multiples
        ];
    }

    /**
     * 获取表单数据
     *
     * @param mixed $key
     * @param mixed $def
     * @return mixed
     */
    public function data($key = null, $def = null)
    {
        if ($key === null) {
            return $this->data;
        }

        return getvalue($this->data, $key, $def);
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
     * 设置提交按钮label
     *
     * @param $label
     * @return $this
     */
    public function setSubmitBtnLabel($label)
    {
        $this->options['submitBtnLabel'] = &$label;
        return $this;
    }

    /**
     * 设置重置按钮label
     *
     * @param $label
     * @return $this
     */
    public function setResetBtnLabel($label)
    {
        $this->options['resetBtnLabel'] = &$label;
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
     * Register custom field.
     *
     * @param string $abstract
     * @param string $class
     *
     * @return void
     */
    public static function extend($abstract, $class)
    {
        static::$availableFields[$abstract] = $class;
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
        if ($className = getvalue(static::$availableFields, $method)) {
            $name = getvalue($arguments, 0, '');

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
        // 保存csrf token
        Admin::setCsrfToken();

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
