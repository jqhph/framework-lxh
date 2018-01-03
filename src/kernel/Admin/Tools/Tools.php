<?php

namespace Lxh\Admin\Tools;

use Lxh\Contracts\Support\Renderable;
use Lxh\Support\Collection;

class Tools implements Renderable
{
    /**
     * Collection of tools.
     *
     * @var Collection
     */
    protected $tools = [];

    /**
     * Append tools.
     *
     * @param Renderable|string $tool
     *
     * @return $this
     */
    public function append($tool)
    {
        $this->tools[] = $tool;

        return $this;
    }

    /**
     * Prepend a tool.
     *
     * @param Renderable|string $tool
     *
     * @return $this
     */
    public function prepend($tool)
    {
        array_unshift($this->tools, $tool);

        return $this;
    }

    /**
     * Render header tools bar.
     *
     * @return string
     */
    public function render()
    {
        $tools = '';
        foreach ($this->tools as &$tool) {
            if ($tool instanceof Renderable) {
                $tools .= $tool->render();
            } elseif ($tool instanceof \Closure) {
                $tools = $tool($this);
            } else {
                $tools .= $tool;
            }
        }

        return $tools;
    }
}
