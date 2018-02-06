<?php

namespace Lxh\Admin\Table;

use Lxh\Admin\Table\Table;
use Lxh\Admin\Widgets\Widget;
use Lxh\Contracts\Support\Renderable;
use Lxh\Support\Arr;

class Column extends Widget
{
    /**
     * @var string
     */
    protected $defaultTitle = '&nbsp;';

    /**
     * @var Tr
     */
    protected $tr;

    /**
     * @var Th|mixed
     */
    protected $th;

    /**
     * @var string
     */
    protected $content = '';

    protected $callback;
    
    public function __construct($title, $content = null)
    {
        if (is_callable($title) && $content === null && !is_string($title)) {
            $this->th = new Th(null, $this->defaultTitle);

            return $this->content = $title;
        }

        $this->th = new Th(null, $title ?: $this->defaultTitle);
        $this->content = $content;
    }

    public function tr(Tr $tr)
    {
        $this->tr = $tr;
        return $this;
    }

    /**
     * 获取列标题
     *
     * @return string
     */
    public function title()
    {
        return $this->th->render();
    }

    public function render()
    {
        $td = new Td();

        if (!is_string($this->content) && is_callable($this->content)) {
            $td->value(call_user_func(
                $this->content,
                $this->tr->items(),
                $td,
                $this->th,
                $this->tr
            ));
            return $td->render();
        }
        $td->value($this->content);
        return $td->render();
    }
}
