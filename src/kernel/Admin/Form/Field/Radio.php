<?php

namespace Lxh\Admin\Form\Field;

use Lxh\Admin\Form\Field;
use Lxh\Contracts\Support\Arrayable;

class Radio extends Field
{
//    protected static $css = [
//        '/packages/admin/AdminLTE/plugins/iCheck/all.css',
//    ];
//
//    protected static $js = [
//        'packages/admin/AdminLTE/plugins/iCheck/icheck.min.js',
//    ];

    protected $view = 'admin::form.radio';

    /**
     *
     * @var array
     */
    protected $options = [];

    protected $inline = 'radio-inline';

    protected $type = 'radio';

    protected $color = 'primary';

    /**
     * Set options.
     *
     * @param array|callable|string $options
     *
     * @return $this
     */
    public function options($options = [])
    {
        if ($options instanceof Arrayable) {
            $options = $options->toArray();
        }

        $this->options = (array) $options;

        return $this;
    }

    public function disabledInline()
    {
        $this->inline = '';

        return $this;
    }

    /**
     * Set options.
     *
     * @param array|callable|string $values
     *
     * @return $this
     */
    public function values($values)
    {
        return $this->options($values);
    }

    protected function variables()
    {
        $vars = parent::variables(); // TODO: Change the autogenerated stub

        $vars['inline'] = $this->inline;
        $vars['type']   = $this->type;
        $vars['color']  = $this->color;

        return $vars;
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $this->formatOptions();
        $this->attribute('type', $this->type);

        return parent::render();
    }
}
