<?php

namespace Lxh\Admin\Fields;

use Lxh\Admin\Admin;
use Lxh\Admin\Form\Field\Text;
use Lxh\Exceptions\InvalidArgumentException;
use Lxh\Support\Collection;

class Editable extends Field
{
    protected static $js = [
        '@lxh/plugins/bootstrap-editable/js/bootstrap-editable.min',
    ];

    protected static $css = [
        '@lxh/plugins/bootstrap-editable/css/bootstrap-editable'
    ];

    /**
     * @var bool
     */
    protected static $setedOptions = false;

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
     * @var string
     */
    protected $placement = 'right';

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
    public function select(array $options = [])
    {
        $source = [];

        $this->type = 'select';

        if (static::$setedOptions === false) {
            static::$setedOptions = true;
            $this->addOptions(['source' => $this->formatOptions($options)]);
        }
    }

    protected function formatOptions(array &$options)
    {
        $new = [];
        foreach ($options as $k => &$v) {
            if (is_array($v) && !empty($v['text'])) {
                $new[] = $v;
                continue;
            }
            $value = $v;
            if (is_string($k)) {
                $new[] = [
                    'value' => $value,
                    'text' => $k
                ];
                continue;
            }
            $new[] = [
                'value' => $value,
                'text' => trans_option($value, $this->name)
            ];
        }

        return $new;
    }

    /**
     * Date type editable.
     */
    public function date()
    {
        return $this->combodate();
    }

    /**
     * Datetime type editable.
     */
    public function datetime()
    {
        return $this->combodate('YYYY-MM-DD HH:mm:ss');
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
        return $this->combodate('MM');
    }

    /**
     * Day type editable.
     */
    public function day()
    {
        return $this->combodate('DD');
    }

    /**
     * Combodate type editable.
     *
     * @param string $format
     * @return $this
     */
    public function combodate($format = 'YYYY-MM-DD')
    {
        $this->type = 'combodate';
        // 加载momentjs
        static::$js['moment'] = '@lxh/js/moment.min';

        $this->addOptions([
            'format'     => $format,
            'viewformat' => $format,
            'template'   => $format,
            'combodate'  => [
                'maxYear' => 2035,
            ],
        ]);

        return $this;
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

    /**
     * @return $this
     */
    public function left()
    {
        $this->placement = 'left';
        return $this;
    }

    /**
     *
     * @return string
     * @throws InvalidArgumentException
     */
    public function render()
    {
        if (empty($this->type)) $this->type = 'text';

        $this->options['name'] = $column = $this->name;

        $class = 'grid-edit-'.
            $this->type.str_replace(['.', '#', '[', ']'], '-', $column);

        $options = json_encode($this->options);

        // 同样的类型只初始化一次
        $this->script(
            'editable.' . $this->type,
            "$('.$class').editable($options);"
        );

        if (!$id = $this->getModelId()) {
            throw new InvalidArgumentException("Id not found!");
        }

        $url = $this->url ?: Admin::url()->updateField($id);

        $attributes = [
//            'href'       => '#',
            'class'      => &$class,
            'data-type'  => $this->type,
            'data-url'   => &$url,
            'data-value' => &$this->value,
            'data-pk' => $id,
            'data-placement' => &$this->placement,
        ];
        if ($this->title) {
            $attributes['data-title'] = $this->title;
        }

        $attributes = (new Collection($attributes))->map(function ($attribute, $name) {
            return "$name='$attribute'";
        })->implode(' ');

        $html = $this->type === 'select' ? '' : $this->value;

        return "<a $attributes>{$html}</a>";
    }
}
