<?php

namespace Lxh\Admin\Grid;

use Lxh\Admin\Fields\Button;
use Lxh\Admin\Grid;
use Lxh\Admin\Models\Admin;

class LayoutSwitcher
{
    /**
     * @var Grid
     */
    protected $grid;

    public function __construct(Grid $grid)
    {
        $this->grid = $grid;
    }

    /**
     * @return string
     */
    public function render()
    {
        $card = new Button();
        $card->color('default')->value('<i class="fa fa-th"></i>');

        $table = new Button();
        $table->color('default')->value('<i class="fa fa-list"></i>');

        return $card->render() . $table->render();
    }
}
