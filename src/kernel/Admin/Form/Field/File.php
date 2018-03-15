<?php

namespace Lxh\Admin\Form\Field;

use Lxh\Admin\Admin;
use Lxh\Admin\Form\Field;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class File extends Field
{
    protected static $css = [
        '@lxh/packages/bootstrap-fileinput/css/fileinput.min',
    ];

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
     * Set default options form image field.
     *
     * @return void
     */
    protected function setupDefaultOptions()
    {
        $defaultOptions = [
            'uploadAsync'          => false,
            'overwriteInitial'     => true,
            'initialPreviewAsData' => true,
            'browseLabel'          => trans('Browse'),
//            'showRemove'           => false,
            'showUpload'           => false,
            'showCancel'           => false,
            'dropZoneEnabled'      => false,
            'deleteExtraData'      => [
                '_token'           => '',
                'id'               => '',
            ],
//            'uploadUrl'            => $url->upload(),
//            'deleteUrl'            => $url->deleteFile(),
            'autoReplace'          => true,
        ];

        $this->options($defaultOptions);
    }

    /**
     * 设置删除文件路径url
     *
     * @param string $url
     * @return $this
     */
    public function deleteUrl($url)
    {
        $this->options['deleteUrl'] = $url;

        return $this;
    }

    /**
     * 设置异步文件上传文件url
     *
     * @param string $url
     * @return $this
     */
    public function uploadUrl($url)
    {
        $this->options['uploadUrl'] = $url;

        return $this;
    }

    /**
     * Allow use to remove file.
     *
     * @return $this
     */
    public function removable()
    {
        
        return $this;
    }

    /**
     * Set options for file-upload plugin.
     *
     * @param array $options
     *
     * @return $this
     */
    public function options($options = [])
    {
        $this->options = array_merge($this->options, $options);

        return $this;
    }

    /**
     * Get file visit url.
     *
     * @param $path
     *
     * @return string
     */
    public function objectUrl($path)
    {
        if (is_valid_url($path)) {
            return $path;
        }

        return rtrim(config('admin.upload.host'), '/').'/'.trim($path, '/');
    }

    /**
     * Set preview options form image field.
     *
     * @return void
     */
    protected function setupPreviewOptions()
    {
        $this->options([
            'initialPreview'       => $this->preview(),
            'initialPreviewConfig' => $this->initialPreviewConfig(),
        ]);
    }

    /**
     * Preview html for file-upload plugin.
     *
     * @return string
     */
    protected function preview()
    {
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

    public function accept($accept)
    {
        return $this->attribute('accept', $accept);
    }

    /**
     * Render file upload field.
     *
     * @return \Lxh\Contracts\View\Factory|\Lxh\View\View
     */
    public function render()
    {
        $this->prepend('<i class="zmdi zmdi-attachment-alt"></i>');
        $this->options['maxFileCount'] = 1;

        if (!isset($this->options['initialCaption'])) {
            $this->options['initialCaption'] = $this->initialCaption($this->value);
        }

        if (!empty($this->value)) {
            $this->setupPreviewOptions();
        }

        $options = json_encode($this->options);

        $this->class('fileinput');

        $this->script = "$(\"{$this->getElementClassSelector()}\").fileinput({$options}).on('filecleared', function (e) {
            $(this).data('value', '');
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
