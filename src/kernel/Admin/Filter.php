<?php

namespace Lxh\Admin;

use Lxh\Admin\Fields\Button;
use Lxh\Admin\Filter\AbstractFilter;
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
 * Class Filter.
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

    /**
     * @var string
     */
    protected $title = 'Search';

    /**
     * @var Grid
     */
    protected $grid;

    /**
     * @var array
     */
    protected $options = [
        'collapsable' => true,
        'enableReset' => true,
        'useBox'      => true,
        'useModal'    => false
    ];

    /**
     * @var array
     */
    protected $fields = [];

    /**
     * 条件查询处理器
     *
     * @var array
     */
    protected $conditions = [];

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

    }

    protected function setupAttributes()
    {
        $url = '';
        if ($this->grid->allowPjax()) {
            $url = request()->url()->string();
        }

        $this->attributes = [
            'method' => 'post',
            'action' => &$url
        ];
    }

    /**
     * @param Grid|null $grid
     * @return $this
     */
    public function grid(Grid $grid = null)
    {
        $this->grid = $grid;
        return $this;
    }

    /**
     * 禁止使用盒子包含过滤器表单
     *
     * @return static
     */
    public function disableBox()
    {
        $this->options['useBox'] = false;

        return $this;
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
        $this->setupAttributes();

        foreach ($this->fields as $field) {
            $field->condition();
        }

        // pjax异步加载，无需重新渲染表单
        if (I('_pjax')) {
            return '';
        }

        if (! $this->options['useBox']) {
            return view($this->view, $this->vars())->render();
        }

        $box = new Box($this->title);

        $box->content(view($this->view, $this->vars())->render())->style('primary');

        if ($this->options['collapsable']) {
            $box->collapsable();
        }

        return $box->render();
    }

    /**
     * 获取字段数组
     *
     * @return array
     */
    public function fields()
    {
        return $this->fields;
    }

    /**
     * 存储条件处理器
     *
     * @return static
     */
    public function condition(AbstractFilter $condition)
    {
        $this->conditions[] = $condition;

        return $this;
    }

    /**
     * 获取条件查询处理器数组
     *
     * @return array
     */
    public function conditions()
    {
        return $this->conditions;
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
    protected function pushField(Field $field, $className = null)
    {
        array_push($this->fields, $field);

        $field->setFilter($this);

        $className = $className ?: get_class($field);

        Admin::addAssetsFieldClass($className);
        Admin::addScriptClass($className);

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
