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

        $this->options(['overwriteInitial' => true]);

        $options = json_encode($this->options);

        $this->script = <<<EOT
$("input{$this->getElementClassSelector()}").fileinput({$options});
EOT;

        return parent::render();
    }
}
