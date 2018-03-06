<?php

namespace Lxh\Admin\Controllers;

use Lxh\MVC\Controller;
use Lxh\Http\Files;

class Image extends Controller
{

    /**
     * 图片读取接口
     *
     * @param array $params
     * @return string
     */
    public function actionRead(array $params)
    {
        if (empty($params['filename']) || empty($params['dir'])) {
            $this->response->withStatus(404);
            return '';
        }
        // 关闭调试信息输出
        $this->withConsoleOutput(false);

        $image = new Files\Image(
            $this->getImageUploadDirectory(),
            $params['dir'].'/'.$params['filename']
        );

        return $image->read();
    }


    /**
     * Default directory for file to upload.
     *
     * @return mixed
     */
    public function getImageUploadDirectory()
    {
        return config('admin.upload.directory.image', __ROOT__ . 'resource/uploads/images');
    }

    /**
     * Default directory for file to upload.
     *
     * @return mixed
     */
    public function getFileUploadDirectory()
    {
        return config('admin.upload.directory.file', __ROOT__ . 'resource/uploads/files');
    }
}
