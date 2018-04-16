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
 * @method Field\MultipleFile   multipleFile($name, $label = '')
 * @method Field\Image          image($name, $label = '')
 * @method Field\MultipleImage  multipleImage($name, $label = '')
 * @method Field\Date           date($name, $label = '')
 * @method Field\Datetime       datetime($name, $label = '')
 * @method Field\Time           time($name, $label = '')
 * @method Field\DateRange      dateRange($name, $start = '', $end = '')
 * @method Field\DateTimeRange  dateTimeRange($name, $start = '', $end = '')
 * @method Field\TimeRange      timeRange($name, $start = '', $end = '')
 * @method Field\Month          month($name, $label = '')
 * @method Field\Year           year($name, $label = '')
 * @method Field\Number         number($name, $label = '')
 * @method Field\Currency       currency($name, $label = '')
 * @method Field\Rate           rate($name, $label = '')
 * @method Field\Divide         divide()
 * @method Field\Decimal        decimal($column, $label = '')
 * @method Field\Html           html($column = '', $label = '')
 * @method Field\Icon           icon($column, $label = '')
 * @method Field\TableCheckbox  tableCheckbox($column, $label = '')
 */
class Form implements Renderable
{
    /**
     * Available fields.
     *
     * @var array
     */
    public static $availableFields = [
        'button'         => \Lxh\Admin\Form\Field\Button::class,
        'checkbox'       => \Lxh\Admin\Form\Field\Checkbox::class,
        'color'          => \Lxh\Admin\Form\Field\Color::class,
        'currency'       => \Lxh\Admin\Form\Field\Currency::class,
        'date'           => \Lxh\Admin\Form\Field\Date::class,
        'dateRange'      => \Lxh\Admin\Form\Field\DateRange::class,
        'datetime'       => \Lxh\Admin\Form\Field\Datetime::class,
        'dateTimeRange'  => \Lxh\Admin\Form\Field\DatetimeRange::class,
        'decimal'        => \Lxh\Admin\Form\Field\Decimal::class,
        'divider'        => \Lxh\Admin\Form\Field\Divide::class,
        'divide'         => \Lxh\Admin\Form\Field\Divide::class,
        'embeds'         => \Lxh\Admin\Form\Field\Embeds::class,
        'editor'         => \Lxh\Admin\Form\Field\Editor::class,
        'email'          => \Lxh\Admin\Form\Field\Email::class,
        'file'           => \Lxh\Admin\Form\Field\File::class,
        'hidden'         => \Lxh\Admin\Form\Field\Hidden::class,
        'id'             => \Lxh\Admin\Form\Field\Id::class,
        'image'          => \Lxh\Admin\Form\Field\Image::class,
        'ip'             => \Lxh\Admin\Form\Field\Ip::class,
        'map'            => \Lxh\Admin\Form\Field\Map::class,
        'mobile'         => \Lxh\Admin\Form\Field\Mobile::class,
        'month'          => \Lxh\Admin\Form\Field\Month::class,
        'multipleSelect' => \Lxh\Admin\Form\Field\MultipleSelect::class,
        'number'         => \Lxh\Admin\Form\Field\Number::class,
        'password'       => \Lxh\Admin\Form\Field\Password::class,
        'radio'          => \Lxh\Admin\Form\Field\Radio::class,
        'rate'           => \Lxh\Admin\Form\Field\Rate::class,
        'select'         => \Lxh\Admin\Form\Field\Select::class,
        'selectTree'     => \Lxh\Admin\Form\Field\SelectTree::class,
        'slider'         => \Lxh\Admin\Form\Field\Slider::class,
        'text'           => \Lxh\Admin\Form\Field\Text::class,
        'textarea'       => \Lxh\Admin\Form\Field\Textarea::class,
        'time'           => \Lxh\Admin\Form\Field\Time::class,
        'timeRange'      => \Lxh\Admin\Form\Field\TimeRange::class,
        'url'            => \Lxh\Admin\Form\Field\Url::class,
        'year'           => \Lxh\Admin\Form\Field\Year::class,
        'html'           => \Lxh\Admin\Form\Field\Html::class,
        'icon'           => \Lxh\Admin\Form\Field\Icon::class,
        'multipleFile'   => \Lxh\Admin\Form\Field\MultipleFile::class,
        'multipleImage'  => \Lxh\Admin\Form\Field\MultipleImage::class,
        'tableCheckbox'  => \Lxh\Admin\Form\Field\TableCheckbox::class,
        'switch'         => \Lxh\Admin\Form\Field\Switcher::class,
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

        return get_value($this->data, $key, $def);
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
        if ($className = get_value(static::$availableFields, $method)) {
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
        // 保存csrf token
        $token = csrf_token();
        Admin::script("LXHSTORE.CSRFTOKEN = '{$token}';");

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
