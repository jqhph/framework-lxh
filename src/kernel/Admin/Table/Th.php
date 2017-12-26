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
     * Sort arguments.
     *
     * @var array
     */
    protected $sort;

    public function __construct(Table $table, $name, $attributes)
    {
        $this->table = $table;

        $this->name = $name;

        parent::__construct((array) $attributes);
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

        $icon = 'fa-sort';
        $type = 'desc';

        if ($this->isSorted()) {
            $type = $this->sort['type'] == 'desc' ? 'asc' : 'desc';
            $icon .= "-amount-{$this->sort['type']}";
        }

//        $query = app('request')->all();
//        $query = array_merge($query, [$this->grid->model()->getSortName() => ['column' => $this->name, 'type' => $type]]);
//
//        $url = URL::current().'?'.http_build_query($query);
        $url = '';

        return "<a class=\"fa fa-fw $icon\" href=\"$url\"></a>";
    }

    /**
     * Determine if this column is currently sorted.
     *
     * @return bool
     */
    protected function isSorted()
    {
        $this->sort = I('sort');

        return $this->sort == $this->name;
    }

    public function render()
    {
        $attributes = $this->formatAttributes();

        return "<th $attributes>" . trans($this->name, 'fields') . '</th>';
    }
}
