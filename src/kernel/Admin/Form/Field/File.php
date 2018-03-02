<?php

namespace Lxh\Admin\Form\Field;

use Lxh\Admin\Form\Field;
use Lxh\Support\Facades\Validator;
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

    /**
     * Create a new File instance.
     *
     * @param string $column
     * @param mixed  $label
     */
    public function __construct($column, $label = '')
    {
        $this->initStorage();

        parent::__construct($column, $label);
    }

    /**
     * Default directory for file to upload.
     *
     * @return mixed
     */
    public function defaultDirectory()
    {
        return config('admin.upload.directory.file');
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
     * 允许上传的类型
     *
     * @param array $types
     * @return $this
     */
    public function allowFileExtensions(array $types)
    {
        $this->options['allowedFileExtensions'] = &$types;
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
        $this->setupDefaultOptions();

        if (!empty($this->value)) {
            $this->attribute('data-initial-preview', $this->preview());

            $this->setupPreviewOptions();
        }

        $options = json_encode($this->options);

        $this->script = <<<EOT
$("{$this->getElementClassSelector()}").fileinput({$options});
EOT;

        return parent::render();
    }
}
