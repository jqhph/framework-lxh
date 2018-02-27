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
use Lxh\Admin\Widgets\Modal;
use Lxh\Admin\Widgets\WaterFall\Card;
use Lxh\Admin\Widgets\Widget;
use Lxh\Contracts\Support\Renderable;
use Lxh\Helper\Util;
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
    const LAYOUT_MODAL = 'modal';
    const LAYOUT_INTABLE = 'inTable';

    /**
     * @var string
     */
    protected $view = 'admin::filter';

    /**
     * @var string
     */
    protected $title = 'Filter';

    /**
     * 弹窗宽度
     *
     * @var string
     */
    protected $modalWidth = '42%';

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
        'layout'      => 'inTable',// 默认表格内
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

    /**
     * @var string
     */
    protected $containerId;

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
            $url = $this->grid->getUrl()->string();
        }

        $this->attributes = [
            'method' => 'get',
            'action' => &$url
        ];
    }

    /**
     * 设置弹窗显示搜索框
     *
     * @return $this
     */
    public function useModal($width = null)
    {
        if ($width) {
            $this->modalWidth = $width;
        }

        $this->options['layout'] = static::LAYOUT_MODAL;

        return $this;
    }

    /**
     * @return bool
     */
    public function allowedUseModal()
    {
        return $this->options['layout'] == static::LAYOUT_MODAL;
    }

    /**
     * @return bool
     */
    public function allowedInTable()
    {
        return $this->options['layout'] == static::LAYOUT_INTABLE;
    }

    /**
     * @return string
     */
    public function getContainerId()
    {
        if (! $this->containerId) {
            $this->containerId = 'f'.Util::randomString(6);
        }

        return $this->containerId;
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
     * @param null $title
     * @return $this
     */
    public function title($title = null)
    {
        $this->title = $title;

        return $this;
    }

    public function render()
    {
        $this->setupAttributes();

        foreach ($this->fields as $field) {
            $field->condition();
        }

        // pjax异步加载，无需重新渲染表单
        if (Grid::isPjaxRequest()) {
            return '';
        }

        if ($this->options['layout'] == static::LAYOUT_INTABLE) {
            $fields = '';
            foreach ($this->fields as $field) {
                $fields .= $field->render();
            }

            return <<<EOF
<form {$this->formatAttributes()} pjax-container>{$fields}{$this->buildFooter()}</form>
EOF;
        }

        return $this->buildModal();
    }

    /**
     * @return string
     */
    protected function buildHtml()
    {
        $fields = '';
        foreach ($this->fields as $field) {
            $fields .= $field->render();
        }

        $footers = $this->buildFooter();
        if ($footers) {
            $style = '';
            if (!$this->options['layout'] == static::LAYOUT_MODAL) {
                $style = 'height:5px;';
            }

            $footers = <<<EOF
<div class="box-footer" style="padding:10px 0 0;"><div class="col-sm-12">$footers</div><div style="clear:both;$style"></div></div>
EOF;
        }

        return <<<EOF
<form {$this->formatAttributes()} pjax-container>
    <div class="box-body fields-group">$fields<div style="clear:both;"></div></div>$footers
</form>
EOF;

    }

    /**
     * 弹窗
     *
     * @return array|mixed
     */
    protected function buildModal()
    {
        foreach ($this->fields as &$field) {
            $field->multipleFieldWidth(2.5);
        }

        $modal = new Modal(trans($this->title), $this->buildHtml());

        $modal->id($this->getContainerId());
        $modal->width($this->modalWidth);
        $modal->disableCloseBtn();

        return $modal->render();
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

    protected function buildFooter()
    {
        $submit = $this->buildSubmitBtn()->render();

        $reset = '';
        if ($this->options['enableReset']) {
            $reset = $this->buildResetBtn()->render();
        }

        $close = '';
        if ($this->options['layout'] == static::LAYOUT_MODAL) {
            $close = new Button(trans('Close'));
            $close = $close->color('default')
                ->attribute('data-dismiss', 'modal')
                ->render();
        }

        return "<div class='filter-input'><div class=\"btn-group\">{$submit}</div>&nbsp; <div class=\"btn-group\">{$reset} {$close}</div></div>";
    }

    /**
     * @return Button
     */
    protected function buildSubmitBtn()
    {
        $submit = new Button('<i class="fa fa-search"></i>&nbsp; ' . trans('Search') );
        $submit->attribute('type', 'submit');

        if ($this->options['layout'] == static::LAYOUT_MODAL) {
            Admin::script(<<<EOF
$(document).on('pjax:send', function () {\$('#{$this->getContainerId()}').modal('hide')});
EOF
);
        }

        return $submit;
    }

    protected function buildResetBtn()
    {
        $reset = new Button(trans('Reset'));
        $reset->attribute('type', 'reset')
            ->color('default')
            ->icon('fa fa-undo');

        return $reset;
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
