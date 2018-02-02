<?php

namespace Lxh\Admin\Fields;

use Lxh\Admin\Admin;
use Lxh\Admin\Form\Field\Text;

class Editable extends Field
{
    /**
     * @var array
     */
    protected $arguments = [];

    /**
     * Title of editable.
     *
     * @var string
     */
    protected $title = '';

    /**
     * Type of editable.
     *
     * @var string
     */
    protected $type = '';

    /**
     * Options of editable function.
     *
     * @var array
     */
    protected $options = [];

    /**
     * @var string
     */
    protected $url = '';

    /**
     * Add options for editable.
     *
     * @param array $options
     */
    public function addOptions($options = [])
    {
        $this->options = array_merge($this->options, $options);
    }

    /**
     * Textarea type editable.
     */
    public function textarea()
    {
        $this->type = 'textarea';
        return $this;
    }

    /**
     * Select type editable.
     *
     * @param array $options
     */
    public function select($options = [])
    {
        $source = [];

        $this->type = 'select';

        foreach ($options as $key => &$value) {
            $source[] = [
                'value' => $key,
                'text'  => $value,
            ];
        }

        $this->addOptions(['source' => &$source]);
    }

    /**
     * Date type editable.
     */
    public function date()
    {
        $this->combodate();
    }

    /**
     * Datetime type editable.
     */
    public function datetime()
    {
        $this->combodate('YYYY-MM-DD HH:mm:ss');
    }

    /**
     * Year type editable.
     */
    public function year()
    {
        $this->combodate('YYYY');
    }

    /**
     * Month type editable.
     */
    public function month()
    {
        $this->combodate('MM');
    }

    /**
     * Day type editable.
     */
    public function day()
    {
        $this->combodate('DD');
    }

    /**
     * Combodate type editable.
     *
     * @param string $format
     */
    public function combodate($format = 'YYYY-MM-DD')
    {
        $this->type = 'combodate';
        // 加载momentjs
        $this->js('moment', '@lxh/js/moment.min');

        $this->addOptions([
            'format'     => $format,
            'viewformat' => $format,
            'template'   => $format,
            'combodate'  => [
                'maxYear' => 2035,
            ],
        ]);
    }

    public function number()
    {
        $this->type = 'number';
        return $this;
    }

    public function email()
    {
        $this->type = 'email';
        return $this;
    }

    public function checklist()
    {
        $this->type = 'checklist';
        return $this;
    }

    public function url()
    {
        $this->type = 'url';
        return $this;
    }

    /**
     * @param $type
     * @return $this
     */
    public function type($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @param $title
     * @return $this
     */
    public function title($title)
    {
        $this->title = $title;
        return $this;
    }

    public function render()
    {
        $this->js('editable', '@lxh/plugins/bootstrap-editable/js/bootstrap-editable.min');
        $this->css('editable', '@lxh/plugins/bootstrap-editable/css/bootstrap-editable');

        $this->options['name'] = $column = $this->name;

        $class = 'grid-editable-'.str_replace(['.', '#', '[', ']'], '-', $column);

        $options = json_encode($this->options);

        Admin::script("$('.$class').editable($options);");

        $id = $this->tr->row(Admin::id());

        $url = $this->url ?: Admin::url()->updateField($id);

        $attributes = [
//            'href'       => '#',
            'class'      => "$class",
            'data-type'  => $this->type,
            'data-url'   => &$url,
            'data-value' => &$this->value,
            'data-pk' => $id,
            'data-placement' => 'right',
        ];
        if ($this->title) {
            $attributes['data-title'] = $this->title;
        }

        $attributes = collect($attributes)->map(function ($attribute, $name) {
            return "$name='$attribute'";
        })->implode(' ');

        $html = $this->type === 'select' ? '' : $this->value;

        return "<a $attributes>{$html}</a>";
    }
}
