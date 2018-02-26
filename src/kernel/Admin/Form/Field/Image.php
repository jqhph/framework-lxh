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

    protected function setup()
    {
        $this->options['allowedFileExtensions'] = [
            'bmp', 'jpg', 'png', 'tiff', 'gif', 'jpeg', 'pcx', 'tga', 'exif', 'fpx', 'svg', 'psd', 'cdr', 'pcd', 'dxf', 'ufo', 'eps', 'ai', 'raw', 'WMF'
        ];
        
        $this->script('image', <<<EOF
(function () {
    var c = LXHSTORE.IFRAME.current();
    function rec() { setTimeout(function(){LXHSTORE.IFRAME.height(c)},50) }
    rec();
    $('input[type="file"]').on('change', rec)
})();
EOF
);
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
