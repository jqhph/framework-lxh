<?php

namespace Lxh\Admin\Grid\Edit;

use Lxh\Admin\Admin;
use Lxh\Admin\Data\Items;
use Lxh\Admin\Table\Table;
use Lxh\Admin\Grid\Edit\Field;
use Lxh\Contracts\Support\Renderable;

/**
 * Class Form.
 *
 * @method \Lxh\Admin\Grid\Edit\Field\Text           text($name, $label = '')
 * @method \Lxh\Admin\Grid\Edit\Field\Checkbox       checkbox($name, $label = '')
 * @method \Lxh\Admin\Grid\Edit\Field\Radio          radio($name, $label = '')
 * @method \Lxh\Admin\Grid\Edit\Field\Select         select($name, $label = '')
 * @method \Lxh\Admin\Grid\Edit\Field\SelectTree     selectTree($name, $label = '')
 * @method \Lxh\Admin\Grid\Edit\Field\MultipleSelect multipleSelect($name, $label = '')
 * @method \Lxh\Admin\Grid\Edit\Field\Textarea       textarea($name, $label = '')
 * @method \Lxh\Admin\Grid\Edit\Field\Hidden         hidden($name, $label = '')
 * @method \Lxh\Admin\Grid\Edit\Field\Date           date($name, $label = '')
 * @method \Lxh\Admin\Grid\Edit\Field\Datetime       datetime($name, $label = '')
 * @method \Lxh\Admin\Grid\Edit\Field\Html           html($column = '', $label = '')
 */
class Form implements Renderable
{
    /**
     * Available fields.
     *
     * @var array
     */
    public static $availableFields = [
        'checkbox'       => Field\Checkbox::class,
        'date'           => Field\Date::class,
        'datetime'       => Field\Datetime::class,
        'hidden'         => Field\Hidden::class,
        'multipleSelect' => Field\MultipleSelect::class,
        'radio'          => Field\Radio::class,
        'select'         => Field\Select::class,
        'selectTree'     => Field\SelectTree::class,
        'text'           => Field\Text::class,
        'textarea'       => Field\Textarea::class,
        'html'           => Field\Html::class,
    ];

    /**
     * @var Table
     */
    protected $table;

    /**
     * @var Items
     */
    protected $items;

    /**
     * id
     *
     * @var mixed
     */
    protected $id;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var array
     */
    protected $fields = [];

    public function __construct(Items $items, $id, callable $call)
    {
        $this->items = $items;
        $this->id    = $id;

        call_user_func($call, $this);
    }

    /**
     * 获取主键
     *
     * @return mixed
     */
    public function getPrimaryKey()
    {
        return $this->id;
    }

    /**
     * Add a form field to form.
     *
     * @param Field\Field $field
     *
     * @return $this
     */
    protected function pushField(Field\Field $field, $className = null)
    {
        array_push($this->fields, $field);

        $field->setEditForm($this);

        $className = $className ?: get_class($field);

        Admin::addAssetsFieldClass($className);
        Admin::addScriptClass($className);

        return $this;
    }

    /**
     * 获取css类
     *
     * @return string
     */
    public function getElementClass()
    {
        return $this->class;
    }

    public function setElementClass($class)
    {
        $this->class = $class;

        return $this;
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
     * Render the form.
     *
     * @return string
     */
    public function render()
    {
        Admin::setCsrfToken();

        $contents = '';
        foreach($this->fields as $field):
            $contents .= $field->render();
            $contents .= $field->formatRules();

        endforeach;

        return $contents;
    }

    public function items()
    {
        return $this->items;
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
}
