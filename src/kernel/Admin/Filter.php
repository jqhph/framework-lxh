<?php

namespace Lxh\Admin;

use Lxh\Admin\Fields\Button;
use Lxh\Admin\Filter\Field\DateRange;
use Lxh\Admin\Filter\Field\MultipleSelect;
use Lxh\Admin\Filter\Field\Select;
use Lxh\Admin\Filter\Field\Text;
use Lxh\Admin\Form\Field;
use Lxh\Admin\Table\Table;
use Lxh\Admin\Widgets\Box;
use Lxh\Admin\Widgets\Widget;
use Lxh\Contracts\Support\Renderable;
use Lxh\Admin\Kernel\Url;
use Lxh\MVC\Model;

/**
 * Class Form.
 *
 * @method Text           text($name, $label = '')
 * @method Select         select($name, $label = '')
 * @method MultipleSelect multipleSelect($name, $label = '')
 * @method DateRange dateRange($name, $label = '')
 */
class Filter extends Widget implements Renderable
{
    /**
     * @var string
     */
    protected $view = 'admin::filter';

    protected $title = 'Search';

    /**
     * @var array
     */
    protected $options = [
        'collapsable' => true,
        'enableReset' => true,
    ];

    /**
     * @var array
     */
    protected $fields = [];

    protected static $availableFields = [
        'text' => Text::class,
        'dateRange' => DateRange::class,
        'select' => Select::class,
        'multipleSelect' => MultipleSelect::class,
    ];

    public function __construct($title = '', $attrbutes = [])
    {
        $this->title = trans($title ?: $this->title);

        parent::__construct($attrbutes);

        $this->setup();
    }

    protected function setup()
    {
        $this->attributes = [
            'method' => 'post',
            'action' => ''
        ];
    }

    public function title($title = null)
    {
        if ($title !== null) {
            $this->title = $title;
        }
        return $this;
    }

    public function disableCollaps()
    {
        $this->options['collapsable'] = false;
        return $this;
    }

    public function render()
    {
        $box = new Box($this->title);

        $box->content(view($this->view, $this->vars())->render())->style('primary');

        if ($this->options['collapsable']) {
            $box->collapsable();
        }

        return $box->render();
    }

    protected function vars()
    {
        return [
            'attributes' => $this->formatAttributes(),
            'fields' => $this->fields,
            'filterOptions' => &$this->options,
        ];
    }


    /**
     * Add a form field to form.
     *
     * @param Field $field
     *
     * @return $this
     */
    protected function pushField(Field &$field)
    {
        array_push($this->fields, $field);

        return $this;
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

            Admin::addAssetsFieldClass($className);

            return $element;
        }
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
        $class = get_value(static::$availableFields, $method);

        if (class_exists($class)) {
            return $class;
        }

        return false;
    }

}
