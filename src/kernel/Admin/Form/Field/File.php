<?php

namespace Lxh\Admin\Form\Field;

use Lxh\Admin\Admin;
use Lxh\Admin\Form\Field;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class File extends Field
{
    use UploadField;

    /**
     * Css.
     *
     * @var array
     */
    protected static $css = [
        '@lxh/packages/bootstrap-fileinput/css/fileinput.min',
    ];

    /**
     * Js.
     *
     * @var array
     */
    protected static $js = [
        '@lxh/packages/bootstrap-fileinput/js/plugins/canvas-to-blob.min',
        '@lxh/packages/bootstrap-fileinput/js/fileinput.min',
    ];

    protected $view = 'admin::form.file';

    protected function setup()
    {
        $this->setupDefaultOptions();
    }

    /**
     * Preview html for file-upload plugin.
     *
     * @return string
     */
    protected function preview()
    {
        return $this->objectUrl($this->value);
    }

    /**
     * 自动替换文件
     *
     * @return $this
     */
    public function autoReplace()
    {
        $this->options['autoReplace'] = true;
        return $this;
    }

    /**
     * 允许上传的后缀
     *
     * @param array $types
     * @return $this
     */
    public function allowFileExtensions(array $exts)
    {
        $this->options['allowedFileExtensions'] = &$exts;
        return $this;
    }

    /**
     * 允许上传的类型
     *
     * @param array $types
     * @return $this
     */
    public function allowedFileTypes(array $types)
    {
        $this->options['allowedFileTypes'] = &$types;
        return $this;
    }

    /**
     * Initialize the caption.
     *
     * @param string $caption
     *
     * @return string
     */
    protected function initialCaption($caption)
    {
        return basename($caption);
    }

    /**
     * @return array
     */
    protected function initialPreviewConfig()
    {
        return [
            ['caption' => basename($this->value), 'key' => 0],
        ];
    }

    /**
     * 选择新图时是否覆盖原图
     *
     * @param bool $bool
     * @return $this
     */
    public function overwriteInitial($bool = true)
    {
        $this->options['overwriteInitial'] = $bool;
        return $this;
    }

    /**
     * 显示移除按钮
     *
     * @param bool $bool
     * @return $this
     */
    public function showRemove($bool = true)
    {
        $this->options['showRemove'] = $bool;
        return $this;
    }

    /**
     * @param bool $bool
     * @return $this
     */
    public function showUpload($bool = true)
    {
        $this->options['showRemove'] = $bool;
        return $this;
    }

    /**
     * Render file upload field.
     *
     * @return \Lxh\Contracts\View\Factory|\Lxh\View\View
     */
    public function render()
    {
        if (!isset($this->options['initialCaption'])) {
            $this->options['initialCaption'] = $this->initialCaption($this->value);
        }

        if (!empty($this->value)) {
            $this->setupPreviewOptions();
        }

        $options = json_encode($this->options);

        $this->class('fileinput');

        $this->script = "$(\"{$this->getElementClassSelector()}\").fileinput({$options}).on(\"fileuploaded\", function(event, data) {
        console.log(123123,data);
    });";

        $this->script('file', <<<EOF
(function () {
    var c = LXHSTORE.IFRAME.current();
    function rec() { setTimeout(function(){LXHSTORE.IFRAME.height(c)},50) }
    rec();
    $('input[type="file"]').on('change', rec)
})();
EOF
        );

        return parent::render();
    }
}
