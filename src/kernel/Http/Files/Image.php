<?php

namespace Lxh\Http\Files;

use Symfony\Component\HttpFoundation\File\File;

class Image
{
    protected $directory;

    protected $targetName;

    protected $maxAge = 8640000;

    /**
     *
     * @var \Lxh\Http\Response
     */
    protected $response;

    protected $path = '';

    public function __construct($directory = '', $targetName = '')
    {
        $this->directory = $directory;
        $this->targetName = $targetName;

        $this->response = response();
    }

    /**
     * @return string
     */
    public function getPath()
    {
        if ($this->path) {
            return $this->path;
        }

        return $this->directory.'/'.$this->targetName;
    }

    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    public function read()
    {
        $path = $this->getPath();

        if (!is_readable($path)) {
            $this->response->withStatus(404);
            return '';
        }

        $mtime = filemtime($path);

        if ($this->isNotModify($mtime)) {
            $this->response->withStatus(304);
            return '';
        }

        $file = new File($path, false);

        $this->response->withHeader(
            'Content-Type', $file->getMimeType()
        );

        $this->setEtag($mtime);

        return file_get_contents($path);
    }

    /**
     * 判断服务器是文件否有修改
     *
     * @param  mixed $etag
     * @return bool
     */
    public function isNotModify($etag)
    {
        return $etag == $this->getEtag();
    }

    public function getEtag()
    {
        return isset($_SERVER['HTTP_IF_NONE_MATCH']) ? $_SERVER['HTTP_IF_NONE_MATCH'] : null;
    }

    public function setEtag($etag)
    {
        $this->response->withHeader('Cache-Control', "max-age={$this->maxAge}");
        $this->response->withHeader('Etag', $etag);
//        $this->response->lastModified(gmdate('D, d M Y H:i:s', $etag).' GMT');
//        $this->response->expires(gmdate('D, d M Y H:i:s', $etag + 964000).' GMT');
        return $this;
    }

}
