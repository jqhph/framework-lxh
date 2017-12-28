<?php

namespace Lxh\Admin;

use Closure;
use Lxh\Admin\Exception\Handle;
use Lxh\Admin\Form\Builder;
use Lxh\Admin\Form\Field;
use Lxh\Admin\Form\Field\File;
use Lxh\Admin\Form\Tab;
use Lxh\Database\Eloquent\Model;
use Lxh\Database\Eloquent\Relations\Relation;
use Lxh\Http\Request;
use Lxh\Support\Arr;
use Lxh\Support\Facades\DB;
use Lxh\Support\Facades\Input;
use Lxh\Support\MessageBag;
use Lxh\Support\Str;
use Lxh\Validation\Validator;
use Spatie\EloquentSortable\Sortable;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class Form.
 *
 * @method Field\Text           text($column, $label = '')
 * @method Field\Checkbox       checkbox($column, $label = '')
 * @method Field\Radio          radio($column, $label = '')
 * @method Field\Select         select($column, $label = '')
 * @method Field\MultipleSelect multipleSelect($column, $label = '')
 * @method Field\Textarea       textarea($column, $label = '')
 * @method Field\Hidden         hidden($column, $label = '')
 * @method Field\Id             id($column, $label = '')
 * @method Field\Ip             ip($column, $label = '')
 * @method Field\Url            url($column, $label = '')
 * @method Field\Color          color($column, $label = '')
 * @method Field\Email          email($column, $label = '')
 * @method Field\Mobile         mobile($column, $label = '')
 * @method Field\Slider         slider($column, $label = '')
 * @method Field\Map            map($latitude, $longitude, $label = '')
 * @method Field\Editor         editor($column, $label = '')
 * @method Field\File           file($column, $label = '')
 * @method Field\Image          image($column, $label = '')
 * @method Field\Date           date($column, $label = '')
 * @method Field\Datetime       datetime($column, $label = '')
 * @method Field\Time           time($column, $label = '')
 * @method Field\Year           year($column, $label = '')
 * @method Field\Month          month($column, $label = '')
 * @method Field\DateRange      dateRange($start, $end, $label = '')
 * @method Field\DateTimeRange  datetimeRange($start, $end, $label = '')
 * @method Field\TimeRange      timeRange($start, $end, $label = '')
 * @method Field\Number         number($column, $label = '')
 * @method Field\Currency       currency($column, $label = '')
 * @method Field\HasMany        hasMany($relationName, $callback)
 * @method Field\SwitchField    switch($column, $label = '')
 * @method Field\Display        display($column, $label = '')
 * @method Field\Rate           rate($column, $label = '')
 * @method Field\Divide         divider()
 * @method Field\Password       password($column, $label = '')
 * @method Field\Decimal        decimal($column, $label = '')
 * @method Field\Html           html($html, $label = '')
 * @method Field\Tags           tags($column, $label = '')
 * @method Field\Icon           icon($column, $label = '')
 * @method Field\Embeds         embeds($column, $label = '')
 * @method Field\MultipleImage  multipleImage($column, $label = '')
 * @method Field\MultipleFile   multipleFile($column, $label = '')
 * @method Field\Captcha        captcha($column, $label = '')
 */
class Form
{
    /**
     * @var Builder
     */
    protected $builder;

    /**
     * Submitted callback.
     *
     * @var Closure
     */
    protected $submitted;

    /**
     * Saving callback.
     *
     * @var Closure
     */
    protected $saving;

    /**
     * Saved callback.
     *
     * @var Closure
     */
    protected $saved;

    /**
     * Data for save to current model from input.
     *
     * @var array
     */
    protected $updates = [];

    /**
     * Data for save to model's relations from input.
     *
     * @var array
     */
    protected $relations = [];

    /**
     * Input data.
     *
     * @var array
     */
    protected $inputs = [];

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
        'datetimeRange'  => \Lxh\Admin\Form\Field\DatetimeRange::class,
        'decimal'        => \Lxh\Admin\Form\Field\Decimal::class,
        'display'        => \Lxh\Admin\Form\Field\Display::class,
        'divider'        => \Lxh\Admin\Form\Field\Divide::class,
        'divide'         => \Lxh\Admin\Form\Field\Divide::class,
        'embeds'         => \Lxh\Admin\Form\Field\Embeds::class,
        'editor'         => \Lxh\Admin\Form\Field\Editor::class,
        'email'          => \Lxh\Admin\Form\Field\Email::class,
        'file'           => \Lxh\Admin\Form\Field\File::class,
        'hasMany'        => \Lxh\Admin\Form\Field\HasMany::class,
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
        'switch'         => \Lxh\Admin\Form\Field\SwitchField::class,
        'text'           => \Lxh\Admin\Form\Field\Text::class,
        'textarea'       => \Lxh\Admin\Form\Field\Textarea::class,
        'time'           => \Lxh\Admin\Form\Field\Time::class,
        'timeRange'      => \Lxh\Admin\Form\Field\TimeRange::class,
        'url'            => \Lxh\Admin\Form\Field\Url::class,
        'year'           => \Lxh\Admin\Form\Field\Year::class,
        'html'           => \Lxh\Admin\Form\Field\Html::class,
        'tags'           => \Lxh\Admin\Form\Field\Tags::class,
        'icon'           => \Lxh\Admin\Form\Field\Icon::class,
        'multipleFile'   => \Lxh\Admin\Form\Field\MultipleFile::class,
        'multipleImage'  => \Lxh\Admin\Form\Field\MultipleImage::class,
        'captcha'        => \Lxh\Admin\Form\Field\Captcha::class,
    ];

    /**
     * Ignored saving fields.
     *
     * @var array
     */
    protected $ignored = [];

    /**
     * Collected field assets.
     *
     * @var array
     */
    protected static $collectedAssets = [];

    /**
     * @var Form\Tab
     */
    protected $tab = null;

    /**
     * Remove flag in `has many` form.
     */
    const REMOVE_FLAG_NAME = '_remove_';

    /**
     * Create a new form instance.
     *
     * @param $model
     */
    public function __construct()
    {
        $this->builder = new Builder($this);
    }

    /**
     * @param Field $field
     *
     * @return $this
     */
    public function pushField(Field $field)
    {
        $field->setForm($this);

        $this->builder->fields()->push($field);

        return $this;
    }

    /**
     * @return Builder
     */
    public function builder()
    {
        return $this->builder;
    }

    /**
     * Generate a edit form.
     *
     * @param $id
     *
     * @return $this
     */
    public function edit($id)
    {
        $this->builder->setMode(Builder::MODE_EDIT);
        $this->builder->setResourceId($id);

        $this->setFieldValue($id);

        return $this;
    }

    /**
     * @param $id
     *
     * @return $this
     */
    public function view($id)
    {
        $this->builder->setMode(Builder::MODE_VIEW);
        $this->builder->setResourceId($id);

        $this->setFieldValue($id);

        return $this;
    }

    /**
     * Use tab to split form.
     *
     * @param string  $title
     * @param Closure $content
     *
     * @return $this
     */
    public function tab($title, Closure $content, $active = false)
    {
        $this->getTab()->append($title, $content, $active);

        return $this;
    }

    /**
     * Get Tab instance.
     *
     * @return Tab
     */
    public function getTab()
    {
        if (is_null($this->tab)) {
            $this->tab = new Tab($this);
        }

        return $this->tab;
    }

    /**
     * Destroy data entity and remove files.
     *
     * @param $id
     *
     * @return mixed
     */
    public function destroy($id)
    {
        $ids = explode(',', $id);

        foreach ($ids as $id) {
            if (empty($id)) {
                continue;
            }
            $this->deleteFilesAndImages($id);
        }

        return true;
    }

    /**
     * Remove files or images in record.
     *
     * @param $id
     */
    protected function deleteFilesAndImages($id)
    {
//        $this->builder->fields()->filter(function ($field) {
//            return $field instanceof Field\File;
//        })->each(function (File $file) use ($data) {
//            $file->setOriginal($data);
//
//            $file->destroy();
//        });
    }

    /**
     * Store a new record.
     *
     * @return
     */
    public function store()
    {
    }


    /**
     * Remove ignored fields from input.
     *
     * @param array $input
     *
     * @return array
     */
    protected function removeIgnoredFields($input)
    {
        Arr::forget($input, $this->ignored);

        return $input;
    }


    /**
     * @param string|array $columns
     * @param bool         $hasDot
     *
     * @return bool
     */
    public function invalidColumn($columns, $hasDot = false)
    {
        foreach ((array) $columns as $column) {
            if ((!$hasDot && Str::contains($column, '.')) ||
                ($hasDot && !Str::contains($column, '.'))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Ignore fields to save.
     *
     * @param string|array $fields
     *
     * @return $this
     */
    public function ignore($fields)
    {
        $this->ignored = array_merge($this->ignored, (array) $fields);

        return $this;
    }

    /**
     * @param array        $data
     * @param string|array $columns
     *
     * @return array|mixed
     */
    protected function getDataByColumn($data, $columns)
    {
        if (is_string($columns)) {
            return get_value($data, $columns);
        }

        if (is_array($columns)) {
            $value = [];
            foreach ($columns as $name => &$column) {
                if (!Arr::has($data, $column)) {
                    continue;
                }
                $value[$name] = get_value($data, $column);
            }

            return $value;
        }
    }

    /**
     * Find field object by column.
     *
     * @param $column
     *
     * @return mixed
     */
    protected function getFieldByColumn($column)
    {
        return $this->builder->fields()->first(
            function (Field $field) use ($column) {
                if (is_array($field->column())) {
                    return in_array($column, $field->column());
                }

                return $field->column() == $column;
            }
        );
    }

    /**
     * Set original data for each field.
     *
     * @return void
     */
    protected function setFieldOriginalValue()
    {
        $values = $this->model->toArray();

        $this->builder->fields()->each(function (Field $field) use ($values) {
            $field->setOriginal($values);
        });
    }

    /**
     * Set all fields value in form.
     *
     * @param $id
     *
     * @return void
     */
    protected function setFieldValue($id)
    {
        $relations = $this->getRelations();

        $this->model = $this->model->with($relations)->findOrFail($id);

        $data = $this->model->toArray();

        $this->builder->fields()->each(function (Field $field) use ($data) {
            $field->fill($data);
        });
    }

    /**
     * Set action for form.
     *
     * @param string $action
     *
     * @return $this
     */
    public function setAction($action)
    {
        $this->builder()->setAction($action);

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
        $this->builder()->fields()->each(function ($field) use ($fieldWidth, $labelWidth) {
            /* @var Field $field  */
            $field->setWidth($fieldWidth, $labelWidth);
        });

        $this->builder()->setWidth($fieldWidth, $labelWidth);

        return $this;
    }

    /**
     * Set view for form.
     *
     * @param string $view
     *
     * @return $this
     */
    public function setView($view)
    {
        $this->builder()->setView($view);

        return $this;
    }

    /**
     * Tools setting for form.
     *
     * @param Closure $callback
     */
    public function tools(Closure $callback)
    {
        $callback = $callback->bindTo($this);

        call_user_func($callback, $this->builder->getTools());
    }

    /**
     * Disable form submit.
     *
     * @return $this
     */
    public function disableSubmit()
    {
        $this->builder()->options(['enableSubmit' => false]);

        return $this;
    }

    /**
     * Disable form reset.
     *
     * @return $this
     */
    public function disableReset()
    {
        $this->builder()->options(['enableReset' => false]);

        return $this;
    }
    

    /**
     * Render the form contents.
     *
     * @return string
     */
    public function render()
    {
        return $this->builder->render();
    }

    /**
     * Get or set input data.
     *
     * @param string $key
     * @param null   $value
     *
     * @return array|mixed
     */
    public function input($key, $value = null)
    {
        if (is_null($value)) {
            return get_value($this->inputs, $key);
        }

        return $this->inputs[$key] = $value;
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
     * Remove registered field.
     *
     * @param array|string $abstract
     */
    public static function forget($abstract)
    {
        Arr::forget(static::$availableFields, $abstract);
    }

    /**
     * Find field class.
     *
     * @param string $method
     *
     * @return bool|mixed
     */
    public static function findFieldClass($method)
    {
        $class = get_value(static::$availableFields, $method);

        if (class_exists($class)) {
            return $class;
        }

        return false;
    }

    /**
     * Getter.
     *
     * @param string $name
     *
     * @return array|mixed
     */
    public function __get($name)
    {
        return $this->input($name);
    }

    /**
     * Setter.
     *
     * @param string $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->input($name, $value);
    }

    /**
     * Generate a Field object and add to form builder if Field exists.
     *
     * @param string $method
     * @param array  $arguments
     *
     * @return Field|void
     */
    public function __call($method, $arguments)
    {
        if ($className = static::findFieldClass($method)) {
            $column = get_value($arguments, 0, ''); //[0];

            $element = new $className($column, array_slice($arguments, 1));

            $this->pushField($element);

            return $element;
        }
    }

    /**
     * Render the contents of the form when casting to string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }
}
