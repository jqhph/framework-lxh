<?php

namespace Lxh\Admin\Layout;

class Row
{
    /**
     * @var Column[]
     */
    protected $columns = [];

    /**
     * Row constructor.
     *
     * @param string $content
     */
    public function __construct($content = '')
    {
        if (!empty($content)) {
            $this->column(12, $content);
        }
    }

    /**
     * Add a column.
     *
     * @param int $width
     * @param Column
     */
    public function column($width, $content)
    {
        return $this->columns[] = new Column($content, $width);
    }

    /**
     * Build row column.
     */
    public function build()
    {
        echo '<div class="row">';

        foreach ($this->columns as $column) {
            $column->build();
        }

        echo '</div>';
    }

}
