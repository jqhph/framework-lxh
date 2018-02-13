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
     * @var string
     */
    protected $delimiter = '&nbsp;&nbsp;&nbsp;';

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
     * @param string $d
     * @return $this
     */
    public function setDelimiter($d)
    {
        $this->delimiter = $d;
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
                $tools .= $tool->render() . $this->delimiter;
            } elseif ($tool instanceof \Closure) {
                $tools = $tool($this) . $this->delimiter;
            } else {
                $tools .= $tool . $this->delimiter;
            }
        }

        return rtrim($tools, $this->delimiter);
    }
}
