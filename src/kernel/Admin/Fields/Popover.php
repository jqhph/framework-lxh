<?php

namespace Lxh\Admin\Fields;

use Lxh\Admin\Admin;
use Lxh\Contracts\Support\Renderable;
use Lxh\Helper\Util;

class Popover extends Field
{
    public function render()
    {
        if ($this->value === '' || $this->value === null) {
            return '';
        }
        $this->class('tag-cloud tag-link');

        // 设置js
        $this->setupScript();

        $this->attribute('data-container', 'body');
        $this->attribute('data-toggle', 'popover');
        if (empty($this->attributes['placement'])) {
            $this->attribute('data-placement', 'top');
        }
        return "<span {$this->formatAttributes()}>{$this->value}</span>";
    }

    protected function setupScript()
    {
        $this->script('helper',
            "var _p=$('{$this->getContainerIdSelector()}').find('[data-toggle=\"popover\"]');_p.popover();_p.find(\"i\").css(\"font-size","14px\");"
        );
    }

    /**
     * @return $this
     */
    public function focus()
    {
        return $this->attribute('data-trigger', 'focus');
    }

    /**
     * @return $this
     */
    public function html()
    {
        return $this->attribute('data-html', 'true');
    }

    /**
     * @param $content
     * @return $this
     */
    public function content($content)
    {
        return $this->attribute('data-content', $content);
    }

    /**
     * @param $title
     * @return $this
     */
    public function title($title)
    {
        return $this->attribute('title', $title);
    }

    public function top()
    {
        return $this->attribute('data-placement', 'top');
    }

    public function left()
    {
        return $this->attribute('data-placement', 'left');
    }

    public function right()
    {
        return $this->attribute('placement', 'right');
    }

    public function bottom()
    {
        return $this->attribute('placement', 'bottom');
    }
}
