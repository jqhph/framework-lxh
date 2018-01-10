<?php

namespace Lxh\Admin\Form\Field;

use Lxh\Admin\Admin;
use Lxh\Admin\Form\Field;
use Lxh\Contracts\Support\Arrayable;
use Lxh\Support\Str;

class SelectTree extends Select
{
    /**
     * View for field to render.
     *
     * @var string
     */
    protected $view = 'admin::form.select-tree';

    /**
     * 树层级
     *
     * @var int
     */
    protected $tier = 1;

    /**
     * 子级数据键名
     *
     * @var string
     */
    protected $subsKey = 'subs';

    /**
     * label键名
     *
     * @var string
     */
    protected $labelKey = 'name';

    protected function formatOptions() {
        $this->buildTree();

        return $this->options;
    }

    protected function buildTree()
    {
        $new = [];
        foreach ($this->options as $k => &$row) {
            $new[] = &$row;
            if (empty($row[$this->subsKey])) continue;

            $new = array_merge($new, $this->buildRows($row[$this->subsKey], $this->tier));

            unset($row[$this->subsKey]);
        }

        $this->options = &$new;
    }

    protected function buildRows(array &$options, $tier = 1)
    {
        $new = [];
        $end = count($options) - 1;
        foreach ($options as $k => &$row) {
            $row[$this->labelKey] = $this->formatIndent($tier, $row[$this->labelKey], $k == $end);

            $new[] = &$row;
            if (! empty($row[$this->subsKey])) {
                $this->tier++;
                $new = array_merge($new, $this->buildRows($row[$this->subsKey], $this->tier));
            }
            unset($row[$this->subsKey]);
        }

        return $new;
    }

    /**
     * 缩进处理
     *
     * @param mixed $value
     * @paran bool $end
     * @return string
     */
    protected function formatIndent($tier, $value, $end = false)
    {
        $indent = str_repeat('&nbsp;', $tier * 3);

        if ($end) {
            return "{$indent}└─ {$value}";
        }
        return "{$indent}├─ {$value}";
    }
}
