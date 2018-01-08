<?php

namespace Lxh\Admin\Form\Field;

use Lxh\Admin\Form\Field;
use Lxh\Contracts\Support\Arrayable;
use Lxh\Support\Arr;

class TableCheckbox extends Field
{
    /**
     * @var string
     */
    protected $view = 'admin::form.table-checkbox';

    /**
     * @var int
     */
    protected $columnsNum = 6;

    /**
     * @var string
     */
    protected $color = 'success';

    /**
     * @var string
     */
    protected $disabled = '';

    public function __construct($column, $label)
    {
        parent::__construct($column, $label);

        $this->class('checkbox pull-left ');
        $this->style('margin-left:8px;');
    }

    /**
     * Set options.
     *
     * @param array|callable|string $options
     *
     * @return $this|mixed
     */
    public function options($options = [])
    {
        if ($options instanceof Arrayable) {
            $options = $options->toArray();
        }

        $this->options = &$options;

        return $this;
    }


    /**
     * @param $color
     * @return $this
     */
    public function color($color)
    {
        $this->class('checkbox-' . $color);
        $this->color = false;
        return $this;
    }

    /**
     * 添加行
     *
     * @param array $rows
     * @param int $maxLen
     * @return $this
     */
    public function rows(array $rows, $maxLen = 6)
    {
        $this->options[] = [
            'rows' => $this->formatRows($rows, $maxLen)
        ];
        return $this;
    }

    /**
     * 自动转化为多行
     *
     * @param array $rows
     * @param $maxLen
     * @return array
     */
    protected function formatRows(array &$rows, $maxLen)
    {
        $result = [];
        $row = [];
        $i = 1;
        foreach ($rows as $k => &$v) {
            if (! is_array($v) || empty($v['label'])) {
                $value = $v;
                if (is_string($k)) {
                    $v = [
                        'value' => $value,
                        'label' => $k
                    ];
                } else {
                    $v = [
                        'value' => $value,
                        'label' => trans_option($value, $this->column)
                    ];
                }
            }
            $row[] = $v;
            if ($i == $maxLen) {
                $result[] = $row;
                $row = [];
                $i = 0;
            }
            $i++;
        }
        if ($row) {
            $result[] = &$row;
        }

        return $result;
    }

    /**
     * 设置列数
     *
     * @param $value
     * @return $this
     */
    public function columns($value)
    {
        $this->columnsNum = $value;
        return $this;
    }

    protected function variables()
    {
        $this->color && $this->color($this->color);

        return array_merge(parent::variables(), [
            'columnsNum' => &$this->columnsNum,
            'options' => &$this->options,
            'color' => $this->color
        ]);
    }
}
