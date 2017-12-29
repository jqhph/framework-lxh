<?php

namespace Lxh\Admin\Table;

use Lxh\Admin\Table\Table;
use Lxh\Admin\Widgets\Widget;
use Lxh\Contracts\Support\Renderable;
use Lxh\Support\Arr;

class Th extends Widget
{
    protected $table;

    protected $name;

    /**
     * Is column sortable.
     *
     * @var bool
     */
    protected $sortable = false;

    /**
     * 默认是升序排序，所以点击时使用倒序
     *
     * @var int
     */
    protected $defaultDesc = 1;

    /**
     * 值为null表示使用默认排序
     *
     * @var array
     */
    protected $desc = null;

    public function __construct(Table $table = null, $name = null, $attributes = null)
    {
        $this->table = $table;

        $this->name = $name;

        parent::__construct((array) $attributes);
    }

    public function desc($desc)
    {
        $this->desc = $desc;

        return $this;
    }

    /**
     * Mark this column as sortable.
     *
     * @return static
     */
    public function sortable()
    {
        $this->sortable = true;

        return $this;
    }

    /**
     * Create the column sorter.
     *
     * @return string|void
     */
    public function sorter()
    {
        if (!$this->sortable) {
            return;
        }

        $icon = 'fa-arrows-v';

        if ($this->isSorted()) {
            $this->desc = I('desc', $this->desc);

            $icon = $this->desc ? 'fa-sort-amount-desc' : 'fa-sort-amount-asc';
        }
        if ($this->desc !== null) {
            $icon = $this->desc ? 'fa-sort-amount-desc' : 'fa-sort-amount-asc';
        }

        $url = url();

        $desc = $this->defaultDesc;// 默认升序排序
        if ($this->desc !== null) {
            $desc = !$this->desc;
        }

        $url->query([
            'sort' => $this->name,
            'desc' => $desc
        ]);
        
        $url = $url->string();
        
        return "&nbsp;&nbsp;<a class=\"fa $icon\" href=\"$url\" style='color:#fe8f81'></a>";
    }

    /**
     * Determine if this column is currently sorted.
     *
     * @return bool
     */
    protected function isSorted()
    {
        $sort = I('sort');

        if ($sort) {
            $this->desc = null;
        }

        return $sort == $this->name;
    }

    public function render()
    {
        $attributes = $this->formatAttributes();

        return "<th $attributes>" . trans($this->name, 'fields') . $this->sorter() . '</th>';
    }

}
