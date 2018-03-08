<?php

namespace Lxh\Admin\Form\Field;

use Lxh\Admin\Admin;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Image extends File
{
    public function render()
    {
        $this->attribute('accept', 'image/*');
        $this->prepend('<i class="fa fa-image"></i>');

        $this->options['allowedFileTypes'] = ['image'];

        return parent::render();
    }

    /**
     * Preview html for file-upload plugin.
     *
     * @return string
     */
    protected function preview()
    {
        return $this->objectUrl(Admin::url()->image($this->value));
    }

}
