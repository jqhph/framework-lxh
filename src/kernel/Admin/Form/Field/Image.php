<?php

namespace Lxh\Admin\Form\Field;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class Image extends File
{
    use ImageField;

    /**
     * {@inheritdoc}
     */
    protected $view = 'admin::form.file';

    /**
     *  Validation rules.
     *
     * @var string
     */
    protected $rules = 'image';

    public function autoReplace()
    {
        //autoReplace
    }

    //elCaptionText   设置标题栏提示信息
    // minImageWidth minImageHeight maxImageWidth maxImageHeight uploadAsync uploadUrl

    //method fileuploaded

    /**
     * @param array|UploadedFile $image
     *
     * @return string
     */
    public function prepare($image)
    {
//        if (I(static::FILE_DELETE_FLAG)) {
//            return $this->destroy();
//        }
//
//        $this->name = $this->getStoreName($image);
//
//        $this->callInterventionMethods($image->getRealPath());
//
//        return $this->uploadAndDeleteOriginal($image);
    }
}
