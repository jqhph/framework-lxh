<?php

namespace Lxh\Admin\Cards;

use Lxh\Admin\Data\Items;
use Lxh\Admin\Fields\Button;
use Lxh\Admin\Fields\Code;
use Lxh\Admin\Fields\Editable;
use Lxh\Admin\Fields\Expand;
use Lxh\Admin\Fields\Field;
use Lxh\Admin\Fields\Image;
use Lxh\Admin\Fields\Label;
use Lxh\Admin\Fields\Link;
use Lxh\Admin\Fields\Popover;
use Lxh\Admin\Fields\Switcher;
use Lxh\Admin\Fields\Tag;
use Lxh\Admin\Fields\Checkbox;
use Lxh\Admin\Fields\Traits\Builder;
use Lxh\Admin\Grid;
use Lxh\Admin\Table\RowActions;
use Lxh\Admin\Table\RowSelector;
use Lxh\Admin\Table\Th;
use Lxh\Admin\Table\Tr;
use Lxh\Admin\Table\Tree;
use Lxh\Admin\Widgets\WaterFall;
use Lxh\Admin\Widgets\Widget;
use Lxh\Contracts\Support\Renderable;
use Lxh\Exceptions\InvalidArgumentException;
use Lxh\Helper\Util;
use Lxh\Support\Arr;

/**
 *
 * @method Link link($field, $closure = null);
 * @method Button button($field, $closure = null);
 * @method Label label($field, $closure = null);
 * @method Tag tag($field, $closure = null);
 * @method Checkbox checkbox($field, $closure = null);
 * @method Code code($field, $closure = null);
 * @method Image image($field, $closure = null);
 * @method Popover popover($field, $closure = null);
 * @method Editable editable($field, $closure = null);
 * @method Switcher switch($field, $closure = null);
 * @method string checked($field);
 * @method string email($field);
 */
class Cards extends Widget
{
    use Builder;

    /**
     * @var array
     */
    protected static $fieldsClass = [
        'link' => Link::class,
        'button' => Button::class,
        'label' => Label::class,
        'tag' => Tag::class,
        'checkbox' => Checkbox::class,
        'code' => Code::class,
        'image' => Image::class,
        'popover' => Popover::class,
        'editable' => Editable::class,
        'switch' => Switcher::class,
        'checked' => 'checked',
        'email' => 'email',
    ];

    /**
     * @var WaterFall
     */
    protected $warterFall;

    /**
     * @var Grid
     */
    protected $grid;

    /**
     * @var array
     */
    protected $fields = [];

    /**
     * @var array
     */
    protected $rows = [];

    /**
     * @var callable
     */
    protected $resolving;

    /**
     * @var Items
     */
    protected $currentItems;

    /**
     * @var WaterFall\Card
     */
    protected $currentCard;

    /**
     * @var RowActions
     */
    protected $rowActions;

    /**
     * @var array
     */
    protected $handlers = [
        'field' => [],
    ];

    public function __construct(&$headers = [], &$rows = [], $style = [])
    {
        $this->warterFall = new WaterFall();
        $this->setFields($headers);
        $this->setRows($rows);
        $this->setStyle($style);
        $this->attribute('id', 'c'.Util::randomString(7));
    }

    /**
     * Set card fields.
     *
     * @param array $headers header
     *
     * @return $this
     */
    public function setFields($fields = [])
    {
        $this->fields = &$fields;

        if ($this->fields) {
            $this->normalizeFields();
        }

        return $this;
    }

    /**
     * @param RowActions $rowActions
     * @return $this
     */
    public function setRowActions(RowActions $rowActions)
    {
        $this->rowActions = $rowActions;
        return $this;
    }

    /**
     *
     * @param array $rows
     * @return $this
     */
    public function setRows(&$rows = [])
    {
        if (Arr::isAssoc($rows)) {
            foreach ($rows as $key => &$item) {
                $this->rows[] = [$key, $item];
            }
            return $this;
        }

        $this->rows = &$rows;
        return $this;
    }

    /**
     * 格式化数组
     *
     * @return array
     */
    protected function normalizeFields()
    {
        $new = [];
        foreach ($this->fields as $k => &$v) {
            if (is_int($k) && is_string($v)) {
                if (! $v) continue;
                $new[$v] = [];
            } else {
                $new[$k] = (array)$v;
            }
        }
        $this->fields = &$new;
    }

    /**
     * @param Grid|null $grid
     * @return $this|Grid
     */
    public function grid(Grid $grid = null)
    {
        if ($grid !== null) {
            $this->grid = $grid;
            return $this;
        }
        return $this->grid;
    }

    /**
     * @param $field
     * @return $this
     */
    public function text($field)
    {
        return $this->item($field);
    }

    /**
     * @param $field
     * @return $this
     */
    public function date($field)
    {
        return $this->resolveFiledView('date', $field, $this->item($field));
    }

    /**
     * @param $field
     * @return $this
     */
    public function icon($field)
    {
        return $this->resolveFiledView('icon', $field, $this->item($field));
    }

    /**
     * 获取当前行字段值
     *
     * @param $field
     * @param null $def
     * @return mixed|null
     * @throws InvalidArgumentException
     */
    public function item($field, $def = null)
    {
        if (! $this->currentItems) {
            throw new InvalidArgumentException();
        }
        return $this->currentItems->get($field, $def);
    }

    /**
     * @return WaterFall\Card
     */
    public function card()
    {
        return $this->currentCard;
    }

    /**
     * @return WaterFall
     */
    public function waterFall()
    {
        return $this->warterFall;
    }

    /**
     * @param array $filters
     * @return $this
     */
    public function setFilterOptions(array $filters)
    {
        return $this->warterFall->filters($filters);
    }

    /**
     * @param $field
     * @return $this
     */
    public function select($field)
    {
        return $this->resolveFiledView('select', $field, $this->item($field));
    }

    /**
     * @param callable $then
     * @return $this
     */
    public function resolving($then)
    {
        $this->resolving = $then;
        return $this;
    }

    /**
     * @return RowSelector
     */
    protected function selector()
    {
        if (! $this->rowSelector) {
            $this->rowSelector = new RowSelector($this->grid);
        }

        return $this->rowSelector;
    }

    /**
     * @param string|object $view
     * @param string $field
     * @param mixed $value
     * @return mixed|string
     */
    protected function resolveFiledView($view, $field, $value, \Closure $then = null)
    {
        $method = 'build' . $view;
        if (method_exists($this, $method)) {
            return $this->$method($field, $value);
        }

        $view = str_replace('.', '\\', $view);

        if (strpos($view, '\\') !== false) {
            $class = $view;
        } else {
            $class  = "Lxh\\Admin\\Fields\\{$view}";
        }

        $view = new $class($field, $value);

        $view->setContainerId(
            $this->warterFall->getId()
        )->setItems($this->currentItems);

        if ($then) $then($view, $this);

        return $view;
    }

    /**
     * @return string
     */
    public function render()
    {
        if (empty($this->rows) || ! $this->resolving) {
            return $this->renderNoDataTip();
        }
        // 使用瀑布流布局渲染
        $wf = $this->warterFall;

        foreach ($this->rows as $k => &$row) {
            $this->currentItems = $items = new Items($row, $k);

            $wf->card(function (WaterFall\Card $card) use ($wf, $items) {
                // 保存当前行卡片
                $this->currentCard = $card;

                call_user_func($this->resolving, $this);

                // 行选择器
                $left = '';
                if ($this->grid->option('useRowSelector')) {
                    $selector = $this->selector();
                    // 全选按钮
                    $this->grid->tools()->prepend($selector->renderHead() . '&nbsp;');

                    $left = $selector->setItems($items)->render();
                }

                // 详情页、删除等动作按钮
                $right = '';
                if ($this->rowActions) {
                    $right = $this->rowActions->setItems($items)->render();
                }
                $card->row($left, $right);
            });
        }

        return $wf->render();
    }

    /**
     * @return string
     */
    protected function renderNoDataTip()
    {
        $tip = trans('No Data.');
        return <<<EOF
<div style="margin:15px 0 0 25px;"><span class="help-block" style="margin-bottom:0"><i class="fa fa-info-circle"></i>&nbsp;{$tip}</span></div>
EOF;

    }

    public function __call($method, $parameters)
    {
        if (isset(static::$fieldsClass[$method])) {
            $field = get_value($parameters, 0);
            $then = get_value($parameters, 1);
            return $this->resolveFiledView(
                static::$fieldsClass[$method], $field, $this->item($field), $then
            );
        }

        $p = count($parameters) > 0 ? $parameters[0] : true;
        if ($method == 'class') {
            if (isset($this->attributes[$method])) {
                $this->attributes[$method] = "{$this->attributes[$method]} $p";
            } else {
                $this->attributes[$method] = &$p;
            }
            return $this;
        }
    }
}