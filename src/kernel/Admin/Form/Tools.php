<?php

namespace Lxh\Admin\Form;

use Lxh\Admin\Admin;
use Lxh\Admin\Form;
use Lxh\Contracts\Support\Htmlable;
use Lxh\Contracts\Support\Renderable;
use Lxh\Support\Collection;
use Lxh\Support\Str;

class Tools implements Renderable
{
    /**
     * @var Builder
     */
    protected $form;

    /**
     * Collection of tools.
     *
     * @var Collection
     */
    protected $tools;

    /**
     * @var array
     */
    protected $options = [
        'enableBackButton' => true,
    ];

    /**
     * Create a new Tools instance.
     *
     * @param Builder $builder
     */
    public function __construct(Builder $builder)
    {
        $this->form = $builder;

        $this->tools = new Collection();
    }

    /**
     * @return string
     */
    protected function backButton()
    {
        $script = <<<'EOT'
$('.form-history-back').on('click', function (event) {
    event.preventDefault();back_tab();
});
EOT;

        Admin::script($script);

        $text = trans('Back');

        return <<<EOT
<div class="btn-group pull-right" style="margin-right: 10px">
    <a class="btn btn-sm btn-default form-history-back"><i class="fa fa-arrow-left"></i>&nbsp;$text</a>
</div>
EOT;
    }

    public function listButton()
    {
        return '';
    }

    /**
     * Prepend a tool.
     *
     * @param string $tool
     *
     * @return $this
     */
    public function add($tool)
    {
        $this->tools->push($tool);

        return $this;
    }

    /**
     * Disable back button.
     *
     * @return $this
     */
    public function disableBackButton()
    {
        $this->options['enableBackButton'] = false;

        return $this;
    }

    /**
     * Render header tools bar.
     *
     * @return string
     */
    public function render()
    {
              if ($this->options['enableBackButton']) {
            $this->add($this->backButton());
        }

        return $this->tools->map(function ($tool) {
            if ($tool instanceof Renderable) {
                return $tool->render();
            }

            if ($tool instanceof Htmlable) {
                return $tool->toHtml();
            }

            return (string) $tool;
        })->implode(' ');
    }
}