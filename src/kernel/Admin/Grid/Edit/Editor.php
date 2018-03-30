<?php

namespace Lxh\Admin\Grid\Edit;

use Lxh\Admin\Data\Items;
use Lxh\Admin\Grid;
use Lxh\Admin\Table\Table;
use Lxh\Cache\Item;
use Lxh\Contracts\Support\Renderable;

class Editor implements Renderable
{
    /**
     * @var Grid
     */
    protected $grid;

    /**
     * @var Table
     */
    protected $table;

    /**
     * 字段id
     *
     * @var mixed
     */
    protected $id;

    /**
     * @var Items
     */
    protected $items;

    /**
     * @var array
     */
    protected $columns = [];

    /**
     * @var array
     */
    protected $attributes = [];

    public function __construct(callable $call)
    {
        call_user_func($call, $this);

        $this->initFormAttributes();
    }

    public function setItems(Items $items)
    {
        $this->items = $items;

        return $this;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * 创建表单对象
     *
     * @return $this
     */
    public function form(callable $call, $width = 12)
    {
        return $this->column(function () use ($call) {
            $form = new Form(
                $this->items, $this->id, $call
            );

            $class = $this->getElementClass();

            $form->setElementClass($class);

            return $form->render();
        }, $width);
    }

    /**
     * Initialize the form attributes.
     */
    protected function initFormAttributes()
    {
        $this->attributes = [
            'method'         => 'POST',
            'action'         => '',
            'accept-charset' => 'UTF-8',
            'pjax-container' => true,
        ];
    }

    /**
     * 设置列内容
     *
     * @param $width
     * @param $content
     * @return $this
     */
    public function column($content, $width = 12)
    {
        $this->columns[] = ['content' => &$content, 'width' => $width];

        return $this;
    }

    public function render()
    {
        $this->attributes['class'] = 'form-horizontal '.$this->getElementClass();

        $title = trans('Quick Edit');

        $contents = "<div class=\"quick-edit edit-{$this->id}\"><form {$this->formatAttribute()}>" . $this->buildColumn("<div class='title'>$title</div>", 12);

        foreach ($this->columns as &$val) {
            $contents .= $this->buildColumn($val['content'], $val['width']);
        }

        $contents .= $this->buildColumn($this->buildBtns(), 12);

        return $contents . '</form></div>';
    }

    /**
     * 提交、取消按钮
     *
     * @return string
     */
    protected function buildBtns()
    {
        $reset  = trans('Reset');
        $submit = trans('Submit');
        $calcel = trans('Cancel');

        $attr = "data-id='{$this->id}'";

        return "<input type=\"hidden\"  name=\"__id__\" value=\"{$this->id}\" />
<div class='content'>
<div class='btn-group pull-left btn-group-sm' style='right:8px'><button $attr type='submit' class=\"btn btn-primary waves-effect pull-right\">$submit</button></div>
<div class=\"btn-group pull-left btn-group-sm\">
<button $attr class='btn btn-default waves-effect pull-right cancel'> $calcel </button>
<button $attr type=\"reset\" class=\"btn btn-default waves-effect pull-right\">$reset&nbsp; <i class=\"fa fa-undo\"></i></button>
</div><div style='clear:both'></div>
</div>
";
}

    /**
     * 获取css类
     *
     * @return string
     */
    public function getElementClass()
    {
        return 'form-' . $this->id;
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
     * Format form attributes form array to html.
     *
     * @param array $attributes
     *
     * @return string
     */
    public function formatAttribute($attributes = [])
    {
        $attributes = $attributes ?: $this->attributes;

        $html = [];
        foreach ($attributes as $key => $val) {
            $html[] = "$key=\"$val\"";
        }

        return implode(' ', $html) ?: '';
    }

    public function buildColumn($content, $width)
    {
        $contents = "<div class=\"col-md-{$width}\">";

        if (is_callable($content)) {
            $contents .= $content();
        } else {
            $contents .= $content;
        }

        $contents .= '</div>';

        return $contents;
    }
}
