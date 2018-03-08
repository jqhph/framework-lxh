<?php

namespace Lxh\Admin\Form\Field;

use Lxh\Admin\Form\Field;
use Lxh\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MultipleFile extends File
{
    protected $view = 'admin::form.multiple-file';

    /**
     * @return array|mixed
     */
    public function original()
    {
        if (empty($this->original)) {
            return [];
        }

        return $this->original;
    }

    /**
     * Preview html for file-upload plugin.
     *
     * @return array
     */
    protected function preview()
    {
        $files = $this->value ?: [];

        return array_map([$this, 'objectUrl'], $files);
    }

    /**
     * Initialize the caption.
     *
     * @param array $caption
     *
     * @return string
     */
    protected function initialCaption($caption)
    {
        if (empty($caption)) {
            return '';
        }

        $caption = array_map('basename', $caption);

        return implode(',', $caption);
    }

    /**
     * @return array
     */
    protected function initialPreviewConfig()
    {
        $config = [];

        foreach ((array)$this->value as $index => &$file) {
            $config[] = [
                'caption' => basename($file),
                'key'     => $index,
            ];
        }

        return $config;
    }

    public function max($max)
    {
        $this->options['maxFileCount'] = $max;

        return $this;
    }

    /**
     * Render file upload field.
     *
     * @return \Lxh\Contracts\View\Factory|\Lxh\View\View
     */
    public function render()
    {
        $this->prepend('<i class="zmdi zmdi-attachment-alt"></i>');
        $this->attribute('multiple', true);

        $this->options['overwriteInitial'] = false;
        $this->options['autoReplace'] = false;

        if (!empty($this->value)) {
            $this->options(['initialPreview' => $this->preview()]);
            $this->setupPreviewOptions();
        }

        $options = json_encode($this->options);

        $this->script = <<<EOT
$("{$this->getElementClassSelector()}").fileinput({$options});
EOT;

        return parent::render();
    }

}
