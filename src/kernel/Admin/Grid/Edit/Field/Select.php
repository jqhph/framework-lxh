<?php

namespace Lxh\Admin\Grid\Edit\Field;

use Lxh\Admin\Admin;
use Lxh\Contracts\Support\Arrayable;
use Lxh\Support\Str;

class Select extends Field
{
    protected static $js = [
        '@lxh/plugins/select2/select2.full.min'
    ];

    protected static $css = [
        '@lxh/plugins/select2/select2.min'
    ];

    protected $view = 'admin::filter.select';

    protected $width = ['field' => 2];

    /**
     * @var array
     */
    protected $defaultOption = [];

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

    /**
     * 增加默认选项
     *
     * @param string $value
     * @param string $label
     * @return $this
     */
    public function defaultOption($value = '', $label = '')
    {
        $label = $label ?: trans_option($value, $this->column);

        $this->defaultOption = [
            'value' => $value, 'label' => $label
        ];

        return $this;
    }

    public function render()
    {
        if (empty($this->script)) {
            $selector = $this->getElementClassSelector();
            $this->script = <<<EOF
$("{$selector}").select2({allowClear:{$this->clear},placeholder:"{$this->getPlaceholder()}"});
EOF;
            // 监听表单重置事件
            $this->onFormReset("$('{$selector}').trigger('change.select2')");
        }

        if ($this->options instanceof \Closure) {
            $this->options(call_user_func($this->options, $this->value));
        }

//        $this->options = array_filter($this->options);

        $this->attachAssets();

        $options = $this->formatOptions();

        return view($this->getView(), $this->variables())->with('options', $options)->render();
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

    protected function variables()
    {
        return array_merge(parent::variables(), [
            'defaultOption' => &$this->defaultOption
        ]);
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
            'dataType' => 'JSON'
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

    public function __call($method, $parameters)
    {
        if ($method === 'default') {
            $value = getvalue($parameters, 0);
            if ($value === null || $value === '') {
                return $this;
            }

            return $this->setDefault($value);
        }
        return parent::__call($method, $parameters); // TODO: Change the autogenerated stub
    }
}
