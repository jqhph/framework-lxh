<?php

namespace Lxh\Admin\Form\Field;

use Lxh\Admin\Admin;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Image extends File
{
    public function render()
    {
        $this->options['allowedFileTypes'] = ['image'];

        return parent::render();
    }

}
