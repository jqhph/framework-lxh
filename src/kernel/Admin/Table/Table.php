<?php

namespace Lxh\Admin\Table;

use Lxh\Admin\Fields\Field;
use Lxh\Admin\Table\Th;
use Lxh\Admin\Table\Tr;
use Lxh\Admin\Table\Tree;
use Lxh\Admin\Widgets\Widget;
use Lxh\Contracts\Support\Renderable;
use Lxh\Support\Arr;

class Table extends Widget
{
    /**
     * @var string
     */
    protected $view = 'component.table';

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * @var array
     */
    protected $rows = [];

    /**
     * @var array
     */
    protected $style = [];

    protected $ths = [];

    protected $trs = [];

    protected $defaultPriority = 1;

    protected $treeName = '';

    /**
     * Table constructor.
     *
     * @param array $headers
     * @param array $rows
     * @param array $style
     */
    public function __construct($headers = [], $rows = [], $style = [])
    {
        $this->setHeaders($headers);
        $this->setRows($rows);
        $this->setStyle($style);
    }

    // 使用层级树结构
    public function useTree($name)
    {
        $this->treeName = $name;

        return $this;
    }

    public function treeName()
    {
        return $this->treeName;
    }

    /**
     * Set table headers.
     *
     * @param array $headers
     *
     * @return $this
     */
    public function setHeaders($headers = [])
    {
        $this->headers = &$headers;

        return $this;
    }

    public function headers()
    {
        return $this->headers;
    }

    /**
     * Set table rows.
     *
     * @param array $rows
     *
     * @return $this
     */
    public function setRows($rows = [])
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
     * Set table style.
     *
     * @param array $style
     *
     * @return $this
     */
    public function setStyle($style = [])
    {
        $this->style = $style;

        return $this;
    }

    protected function buildHeaders()
    {
        $th = '';
        foreach ($this->headers as $k => &$options) {
            $th .= $this->buildTh($k, $options)->render();
        }
        return $th;
    }

    /**
     *
     * @param string $name 字段名称
     * @param array $field 配置参数
     * @return Th
     */
    protected function buildTh($name, $options)
    {
        $vars = (array)get_value($options, 'th');

        $vars['data-priority'] = $this->getPriorityFromOptions($options);

        return $this->ths[$name] = new Th($this, $name, $vars);
    }

    public function getPriorityFromOptions($options)
    {
        return get_value($options, 'priority', $this->defaultPriority);
    }

    protected function buildRows()
    {
        $tr = '';
        foreach ($this->rows as $k => &$row) {
            $tr .= $this->buildTr($k, $row)->render();
        }
        return $tr;
    }


    protected function buildTr($k, &$row)
    {
        return $this->trs[] = new Tr($this, $k, $row);
    }


    /**
     * Render the table.
     *
     * @return string
     */
    public function render()
    {
        $vars = [
            'attributes' => $this->formatAttributes(),
            'headers' => $this->buildHeaders(),
            'rows' => $this->buildRows()
        ];
        return view($this->view, $vars)->render();
    }
}
