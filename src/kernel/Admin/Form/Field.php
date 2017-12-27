<?php

namespace Lxh\Admin\Form;

use Lxh\Admin\Admin;
use Lxh\Admin\Widgets\Form;
use Lxh\Contracts\Support\Arrayable;
use Lxh\Contracts\Support\Renderable;
use Lxh\Support\Arr;

/**
 * Class Field.
 *
 * @method Field default($value) set field default value
 */
class Field implements Renderable
{
    const FILE_DELETE_FLAG = '_file_del_';

    /**
     * Element id.
     *
     * @var array|string
     */
    protected $id;

    /**
     * Element value.
     *
     * @var mixed
     */
    protected $value;

    /**
     * Field original value.
     *
     * @var mixed
     */
    protected $original;

    /**
     * Field default value.
     *
     * @var mixed
     */
    protected $default;

    /**
     * Element label.
     *
     * @var string
     */
    protected $label = '';

    /**
     * Column name.
     *
     * @var string
     */
    protected $column = '';

    /**
     * Form element name.
     *
     * @var string
     */
    protected $elementName = [];

    /**
     * Form element classes.
     *
     * @var array
     */
    protected $elementClass = [];

    /**
     * Variables of elements.
     *
     * @var array
     */
    protected $variables = [];

    /**
     * Options for specify elements.
     *
     * @var array
     */
    protected $options = [];

    /**
     * Validation rules.
     *
     * @var string
     */
    protected $rules = '';

    /**
     * Css required by this field.
     *
     * @var array
     */
    protected static $css = [];

    /**
     * Js required by this field.
     *
     * @var array
     */
    protected static $js = [];

    /**
     * Script for field.
     *
     * @var string
     */
    protected $script = '';

    /**
     * Element attributes.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Parent form.
     *
     * @var Form
     */
    protected $form = null;

    /**
     * View for field to render.
     *
     * @var string
     */
    protected $view = '';

    /**
     * Help block.
     *
     * @var array
     */
    protected $help = [];

    /**
     * Key for errors.
     *
     * @var mixed
     */
    protected $errorKey;

    /**
     * Placeholder for this field.
     *
     * @var string|array
     */
    protected $placeholder;

    /**
     * Width for label and field.
     *
     * @var array
     */
    protected $width = [
        'label' => 2,
        'field' => 8,
    ];

    /**
     * Field constructor.
     *
     * @param $column
     * @param string $label
     */
    public function __construct($column, $label = '')
    {
        $this->column = $column;
        $this->label = $this->formatLabel($label);
        $this->id = $this->formatId($column);
    }

    /**
     * Get assets required by this field.
     *
     * @return array
     */
    public static function getAssets()
    {
        return [
            'css' => &static::$css,
            'js' => &static::$js,
        ];
    }

    /**
     * Format the field element id.
     *
     * @param string|array $column
     *
     * @return string|array
     */
    protected function formatId($column)
    {
        return str_replace('.', '_', $column);
    }

    /**
     * Format the label value.
     *
     * @param string $label
     *
     * @return string
     */
    protected function formatLabel($label = '')
    {
        if ($label) return $label;

        $column = is_array($this->column) ? current($this->column) : $this->column;

        return trans($column, 'fields');
    }

    /**
     * Format the name of the field.
     *
     * @param string $column
     *
     * @return array|mixed|string
     */
    protected function formatName($column)
    {
        if (is_string($column)) {
            $name = explode('.', $column);

            if (count($name) == 1) {
                return $name[0];
            }

            $html = array_shift($name);
            foreach ($name as &$piece) {
                $html .= "[$piece]";
            }

            return $html;
        }

        if (is_array($this->column)) {
            $names = [];
            foreach ($this->column as $key => &$name) {
                $names[$key] = $this->formatName($name);
            }

            return $names;
        }

        return '';
    }

    /**
     * Set form element name.
     *
     * @param string $name
     *
     * @return $this
     *
     * @author Edwin Hui
     */
    public function setElementName($name)
    {
        $this->elementName = $name;

        return $this;
    }

    /**
     * Fill data to the field.
     *
     * @param array $data
     *
     * @return void
     */
    public function fill($data)
    {
        // Field value is already setted.
        if (is_array($this->column)) {
            foreach ($this->column as $key => $column) {
                $this->value[$key] = get_value($data, $column);
            }

            return;
        }

        $this->value = get_value($data, $this->column);
    }

    /**
     * Set original value to the field.
     *
     * @param array $data
     *
     * @return void
     */
    public function setOriginal($data)
    {
        if (is_array($this->column)) {
            foreach ($this->column as $key => $column) {
                $this->original[$key] = get_value($data, $column);
            }

            return;
        }

        $this->original = get_value($data, $this->column);
    }

    /**
     * @param Form $form
     *
     * @return $this
     */
    public function setForm(Form $form = null)
    {
        $this->form = $form;

        return $this;
    }

    /**
     * Set width for field and label.
     *
     * @param int $field
     * @param int $label
     *
     * @return $this
     */
    public function setWidth($field = 8, $label = 2)
    {
        $this->width = [
            'label' => $label,
            'field' => $field,
        ];

        return $this;
    }

    /**
     * Set the field options.
     *
     * @param array $options
     *
     * @return $this
     */
    public function options($options = [])
    {
        if ($options instanceof Arrayable) {
            $options = $options->toArray();
        }

        $this->options = array_merge($this->options, $options);

        return $this;
    }

    /**
     * Get or set rules.
     *
     * @param null $rules
     *
     * @return mixed
     */
    public function rules($rules = null)
    {
        if (is_null($rules)) {
            return $this->rules;
        }

        $rules = array_filter(explode('|', "{$this->rules}|$rules"));

        $this->rules = implode('|', $rules);

        return $this;
    }

    /**
     * Get field validation rules.
     *
     * @return string
     */
    protected function getRules()
    {
        return $this->rules;
    }

    /**
     * Remove a specific rule.
     *
     * @param string $rule
     *
     * @return void
     */
    protected function removeRule($rule)
    {
        $this->rules = str_replace($rule, '', $this->rules);
    }

    /**
     * Get key for error message.
     *
     * @return string
     */
    public function getErrorKey()
    {
        return $this->errorKey ?: $this->column;
    }

    /**
     * Set key for error message.
     *
     * @param string $key
     *
     * @return $this
     */
    public function setErrorKey($key)
    {
        $this->errorKey = $key;

        return $this;
    }

    /**
     * Set or get value of the field.
     *
     * @param null $value
     *
     * @return mixed
     */
    public function value($value = null)
    {
        if (is_null($value)) {
            return is_null($this->value) ? $this->getDefault() : $this->value;
        }

        $this->value = $value;

        return $this;
    }

    /**
     * Set default value for field.
     *
     * @param $default
     *
     * @return $this
     */
    public function setDefault($default)
    {
        $this->default = $default;

        return $this;
    }

    /**
     * Get default value.
     *
     * @return mixed
     */
    public function getDefault()
    {
        if ($this->default instanceof \Closure) {
            return call_user_func($this->default, $this->form);
        }

        return $this->default;
    }

    /**
     * Set help block for current field.
     *
     * @param string $text
     * @param string $icon
     *
     * @return $this
     */
    public function help($text = '', $icon = 'fa-info-circle')
    {
        $this->help = compact('text', 'icon');

        return $this;
    }

    /**
     * Get column of the field.
     *
     * @return string
     */
    public function column()
    {
        return $this->column;
    }

    /**
     * Get label of the field.
     *
     * @return string
     */
    public function label()
    {
        return $this->label;
    }

    /**
     * Get original value of the field.
     *
     * @return mixed
     */
    public function original()
    {
        return $this->original;
    }

    /**
     * Sanitize input data.
     *
     * @param array $input
     * @param string $column
     *
     * @return array
     */
    protected function sanitizeInput($input, $column)
    {
        if ($this instanceof Field\MultipleSelect) {
            $value = get_value($input, $column);
            $input[$column] = array_filter($value);
        }

        return $input;
    }

    /**
     * Add html attributes to elements.
     *
     * @param array|string $attribute
     * @param mixed $value
     *
     * @return $this
     */
    public function attribute($attribute, $value = null)
    {
        if (is_array($attribute)) {
            $this->attributes = array_merge($this->attributes, $attribute);
        } else {
            $this->attributes[$attribute] = (string)$value;
        }

        return $this;
    }

    /**
     * Set the field as readonly mode.
     *
     * @return Field
     */
    public function readOnly()
    {
        return $this->attribute('disabled', true);
    }

    /**
     * Set field placeholder.
     *
     * @param string $placeholder
     *
     * @return Field
     */
    public function placeholder($placeholder = '')
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    /**
     * Get placeholder.
     *
     * @return string
     */
    public function getPlaceholder()
    {
        return $this->placeholder ?: trans($this->column . '.input', 'fields');
    }

    /**
     * Format the field attributes.
     *
     * @return string
     */
    protected function formatAttributes()
    {
        $html = [];

        foreach ($this->attributes as $name => &$value) {
            $html[] = $name . '="' . e($value) . '"';
        }

        return implode(' ', $html);
    }

    /**
     * Set form element class.
     *
     * @param string $class
     *
     * @return $this
     */
    public function setElementClass($class)
    {
        $this->elementClass = (array)$class;

        return $this;
    }

    /**
     * Get element class.
     *
     * @return array
     */
    protected function getElementClass()
    {
        if (!$this->elementClass) {
            $name = $this->elementName ?: $this->formatName($this->column);

            $this->elementClass = (array)str_replace(['[', ']'], '_', $name);
        }

        return $this->elementClass;
    }

    /**
     * Get element class string.
     *
     * @return mixed
     */
    protected function getElementClassString()
    {
        $elementClass = $this->getElementClass();

        if (Arr::isAssoc($elementClass)) {
            $classes = [];

            foreach ($elementClass as $index => &$class) {
                $classes[$index] = is_array($class) ? implode(' ', $class) : $class;
            }

            return $classes;
        }

        return implode(' ', $elementClass);
    }

    /**
     * Get element class selector.
     *
     * @return string
     */
    protected function getElementClassSelector()
    {
        $elementClass = $this->getElementClass();

        if (Arr::isAssoc($elementClass)) {
            $classes = [];

            foreach ($elementClass as $index => &$class) {
                $classes[$index] = '.' . (is_array($class) ? implode('.', $class) : $class);
            }

            return $classes;
        }

        return '.' . implode('.', $elementClass);
    }

    /**
     * Add the element class.
     *
     * @param $class
     *
     * @return $this
     */
    public function addElementClass($class)
    {
        if (is_array($class) || is_string($class)) {
            $this->elementClass = array_merge($this->elementClass, (array)$class);

            $this->elementClass = array_unique($this->elementClass);
        }

        return $this;
    }

    /**
     * Remove element class.
     *
     * @param $class
     *
     * @return $this
     */
    public function removeElementClass($class)
    {
        $delClass = [];

        if (is_string($class) || is_array($class)) {
            $delClass = (array)$class;
        }

        foreach ($delClass as &$del) {
            if (($key = array_search($del, $this->elementClass))) {
                unset($this->elementClass[$key]);
            }
        }

        return $this;
    }

    /**
     * Get the view variables of this field.
     *
     * @return array
     */
    protected function variables()
    {
        return array_merge($this->variables, [
            'id' => $this->id,
            'name' => $this->elementName ?: $this->formatName($this->column),
            'help' => &$this->help,
            'class' => $this->getElementClassString(),
            'value' => $this->value(),
            'label' => &$this->label,
            'width' => &$this->width,
            'column' => &$this->column,
            'errorKey' => $this->getErrorKey(),
            'attributes' => $this->formatAttributes(),
            'placeholder' => $this->getPlaceholder(),
        ]);
    }

    public function formatRules()
    {
        if (! $this->rules) {
            return '';
        }
        $rule = json_encode([
            'name' => $this->column, 'rules' => &$this->rules
        ]);

        return "<script>window.formRules.push({$rule})</script>";
    }

    /**
     * Get view of this field.
     *
     * @return string
     */
    public function getView()
    {
        if (!empty($this->view)) {
            return $this->view;
        }

        $class = explode('\\', get_called_class());

        return 'admin::form.' . strtolower(end($class));
    }

    /**
     * Get script of current field.
     *
     * @return string
     */
    public function getScript()
    {
        return $this->script;
    }

    /**
     * Render this filed.
     *
     * @return \Lxh\Contracts\View\Factory|\Lxh\View\View
     */
    public function render()
    {
        $this->attachAssets();

        return view($this->getView(), $this->variables())->render();
    }

    protected function attachAssets()
    {
        Admin::script($this->script);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->render()->render();
    }

    /**
     * @param $method
     * @param $arguments
     *
     * @return $this
     */
    public function __call($method, $arguments)
    {
        if ($method === 'default') {
            return $this->setDefault(get_value($arguments, 0));
        }
    }
}
