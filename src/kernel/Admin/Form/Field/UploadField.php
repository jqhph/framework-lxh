<?php

namespace Lxh\Admin\Form\Field;

use Lxh\Admin\Form;
use Lxh\File\FileManager;
use Lxh\Support\Facades\Storage;
use Lxh\Support\Facades\URL;
use Lxh\Support\MessageBag;
use Symfony\Component\HttpFoundation\File\UploadedFile;

trait UploadField
{
    /**
     * Upload directory.
     *
     * @var string
     */
    protected $directory = '';

    /**
     * File name.
     *
     * @var null
     */
    protected $filename = null;

    /**
     * Storage instance.
     *
     * @var FileManager
     */
    protected $storage = '';

    /**
     * If use unique name to store upload file.
     *
     * @var bool
     */
    protected $useUniqueName = false;

    /**
     * @var bool
     */
    protected $removable = false;

    /**
     * Initialize the storage instance.
     *
     * @return void.
     */
    protected function initStorage()
    {
    }

    /**
     * Set default options form image field.
     *
     * @return void
     */
    protected function setupDefaultOptions()
    {
        $defaultOptions = [
            'overwriteInitial'     => false,
            'initialPreviewAsData' => true,
            'browseLabel'          => trans('admin::lang.browse'),
            'showRemove'           => false,
            'showUpload'           => false,
            'initialCaption'       => $this->initialCaption($this->value),
            'deleteExtraData'      => [
                '_token'                 => '',
                '_method'                => 'PUT',
            ],
        ];

//        if ($this->form instanceof Form) {
//            $defaultOptions['deleteUrl'] = $this->form->resource().'/'.$this->form->model()->getKey();
//        }

        $this->options($defaultOptions);
    }

    /**
     * Set preview options form image field.
     *
     * @return void
     */
    protected function setupPreviewOptions()
    {
        if (!$this->removable) {
            return;
        }

        $this->options([
            //'initialPreview'        => $this->preview(),
            'initialPreviewConfig' => $this->initialPreviewConfig(),
        ]);
    }

    /**
     * Allow use to remove file.
     *
     * @return $this
     */
    public function removable()
    {
        $this->removable = true;

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
        $this->options = array_merge($options, $this->options);

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
     * Generate a unique name for uploaded file.
     *
     * @param UploadedFile $file
     *
     * @return string
     */
    protected function generateUniqueName(UploadedFile $file)
    {
        return md5(uniqid()).'.'.$file->guessExtension();
    }

    /**
     * Destroy original files.
     *
     * @return void.
     */
    public function destroy()
    {
    }
}
