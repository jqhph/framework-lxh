<?php

namespace Lxh\Admin\Tools;

use Lxh\Admin\Data\Items;
use Lxh\Admin\Grid;
use Lxh\Admin\Table\Tr;
use Lxh\Contracts\Support\Renderable;
use Lxh\Support\Collection;

class TrTools extends Tools
{
    /**
     * @var Items
     */
    protected $items;

    /**
     * @var \Closure
     */
    protected $rendering;

    public function __construct(\Closure $closure = null)
    {
        $this->rendering = $closure;
    }

    /**
     * @param Tr $tr
     * @return $this
     */
    public function setItems(Items $items)
    {
        $this->items = $items;
        return $this;
    }

    /**
     * @param \Closure $closure
     * @return $this
     */
    public function rendering(\Closure $closure)
    {
        $this->rendering = $closure;
        return $this;
    }

    /**
     * Render header tools bar.
     *
     * @return string
     */
    public function render()
    {
        if ($rendering = $this->rendering) {
            $rendering($this, $this->items);
        }

        $end = '&nbsp;&nbsp;&nbsp;';
        $tools = '';
        foreach ($this->tools as &$tool) {
            if ($tool instanceof Renderable) {
                $tools .= $tool->render() . $end;
            } elseif ($tool instanceof \Closure) {
                $tools = $tool($this) . $end;
            } else {
                $tools .= $tool . $end;
            }
        }

        return rtrim($tools, $end);
    }
}
