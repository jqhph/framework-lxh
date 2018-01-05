<?php

namespace Lxh\Admin\Form\Field;

use Lxh\Admin\Admin;
use Lxh\Admin\Form\Field;
use Lxh\Contracts\Support\Arrayable;
use Lxh\Support\Str;

class Select extends Field
{
    protected function setup()
    {
        $this->css('select', 'lib/plugins/select2/select2.min');
        $this->js('select', 'lib/plugins/select2/select2.full.min');
    }

    /**
     * 是否允许清除单选框
     *
     * @var string
     */
    protected $clear = 'false';

    public function allowClear()
    {
        $this->clear = 'true';
        return $this;
    }

    public function render()
    {
        if (empty($this->script)) {
            $selector = $this->getElementClassSelector();
            $this->script = <<<EOF
$("{$selector}").select2({allowClear:{$this->clear},placeholder:"{$this->getPlaceholder()}"});
$(document).on('reset.form', function () {\$('{$selector}').trigger('change.select2');})
EOF;
        }

        if ($this->options instanceof \Closure) {
            $this->options(call_user_func($this->options, $this->value));
        }

//        $this->options = array_filter($this->options);

        $this->attachAssets();

        $options = $this->formatOptions();

        return view($this->getView(), $this->variables())->with('options', $options)->render();
    }

    protected function formatOptions()
    {
        foreach ($this->options as $k => &$v) {
            if (is_array($v) && ! empty($v['label'])) {
                continue;
            }
            $value = $v;
            if (is_string($k)) {
                $v = [
                    'value' => $value,
                    'label' => $k
                ];
                continue;
            }
            $v = [
                'value' => $value,
                'label' => trans_option($value, $this->column)
            ];
        }

        return $this->options;
    }

    /**
     * Set options.
     *
     * @param array|callable|string $options
     *
     * @return $this|mixed
     */
    public function options($options = [])
    {
        // remote options
        if (is_string($options)) {
            return call_user_func_array([$this, 'loadOptionsFromRemote'], func_get_args());
        }

        if ($options instanceof Arrayable) {
            $options = $options->toArray();
        }

        if (is_callable($options)) {
            $this->options = $options;
        } else {
            $this->options = (array) $options;
        }

        return $this;
    }

    /**
     * Load options for other select on change.
     *
     * @param string $field
     * @param string $sourceUrl
     * @param string $idField
     * @param string $textField
     *
     * @return $this
     */
    public function load($field, $sourceUrl, $idField = 'id', $textField = 'text')
    {
        if (Str::contains($field, '.')) {
            $field = $this->formatName($field);
            $class = str_replace(['[', ']'], '_', $field);
        } else {
            $class = $field;
        }

        $script = <<<EOT
$(document).on('change', "{$this->getElementClassSelector()}", function () {
    var target = $(this).closest('.fields-group').find(".$class");
    $.get("$sourceUrl?q="+this.value, function (data) {
        target.find("option").remove();
        $(target).select2({
            data: $.map(data, function (d) {
                d.id = d.$idField;
                d.text = d.$textField;
                return d;
            })
        }).trigger('change');
    });
});
EOT;

        Admin::script($script);

        return $this;
    }

    /**
     * Load options from remote.
     *
     * @param string $url
     * @param array  $parameters
     * @param array  $options
     *
     * @return $this
     */
    protected function loadOptionsFromRemote($url, $parameters = [], $options = [])
    {
        $ajaxOptions = [
            'url' => $url.'?'.http_build_query($parameters),
        ];

        $ajaxOptions = json_encode(array_merge($ajaxOptions, $options));

        $this->script = <<<EOT
$.ajax($ajaxOptions).done(function(data) {
  $("{$this->getElementClassSelector()}").select2({data: data});
});

EOT;

        return $this;
    }

    /**
     * Load options from ajax results.
     *
     * @param string $url
     * @param $idField
     * @param $textField
     *
     * @return $this
     */
    public function ajax($url, $idField = 'id', $textField = 'text')
    {
        $this->script = <<<EOT
$("{$this->getElementClassSelector()}").select2({
  ajax: {
    url: "$url",
    dataType: 'json',
    delay: 250,
    data: function (params) {
      return {
        q: params.term,
        page: params.page
      };
    },
    processResults: function (data, params) {
      params.page = params.page || 1;

      return {
        results: $.map(data.data, function (d) {
                   d.id = d.$idField;
                   d.text = d.$textField;
                   return d;
                }),
        pagination: {
          more: data.next_page_url
        }
      };
    },
    cache: true
  },
  minimumInputLength: 1,
  escapeMarkup: function (markup) {
      return markup;
  }
});
EOT;

        return $this;
    }
}
