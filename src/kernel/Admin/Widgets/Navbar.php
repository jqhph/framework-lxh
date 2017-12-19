<?php

namespace Lxh\Admin\Widgets;

use Lxh\Contracts\Support\Htmlable;
use Lxh\Contracts\Support\Renderable;
use Lxh\Support\Collection;

class Navbar implements Renderable
{
    protected $items;

    public function __construct()
    {
        $this->items = new Collection();
    }

    public function add($item)
    {
        $this->items->push($item);

        return $this;
    }

    public function render()
    {
        return $this->items->reverse()->map(function ($item) {
            if ($item instanceof Htmlable) {
                return $item->toHtml();
            }

            if ($item instanceof Renderable) {
                return $item->render();
            }

            return (string) $item;
        })->implode('');
    }
}
