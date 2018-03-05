<?php

namespace Lxh\Admin\Form\Field;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class MultipleImage extends MultipleFile
{
    /**
     * {@inheritdoc}
     */
    protected $view = 'admin::form.multiplefile';

}
