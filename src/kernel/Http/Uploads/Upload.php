<?php

namespace Lxh\Http\Uploads;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class Upload
{
    /**
     * @var UploadedFile
     */
    protected $file;

    /**
     * 文件上传目录
     *
     * @var string
     */
    protected $directory;

    /**
     * 允许的后缀
     *
     * @var array
     */
    protected $allowFileExtensions = [];

    /**
     * 文件上传错误信息
     *
     * @var array
     */
    protected $errors = [];

    /**
     * 文件后缀
     *
     * @var string
     */
    protected $clientExtension;

    /**
     * 文件保存名称
     *
     * @var string
     */
    protected $targetName;

    /**
     * 按天分文件夹
     *
     * @var string
     */
    protected $dateFormat = 'Ymd';

    public function __construct(UploadedFile $file, $directory = '')
    {
        $this->file = $file;
        $this->directory = $directory;
    }

    /**
     * 获取文件上传目录
     *
     * @return string
     */
    public function directory()
    {
        $append = '';
        if ($this->dateFormat) {
            $append = '/' . date($this->dateFormat);
        }

        return $this->directory . $append;
    }

    public function setDirectory($directory)
    {
        $this->directory = $directory;

        return $this;
    }

    public function format($format)
    {
        $this->dateFormat = $format;

        return $this;
    }

    /**
     * 获取文件上传错误信息
     *
     * @return array
     */
    public function errors()
    {
        return $this->errors;
    }

    /**
     * 保存上传文件
     *
     * @return bool|\Symfony\Component\HttpFoundation\File\File
     */
    public function handle()
    {
        if (! $this->file) {
            return false;
        }

        if (
            $this->allowFileExtensions && !in_array($this->guessClientExtension(), $this->allowFileExtensions)
        ) {
            $this->errors[] = sprintf(
                trans('Invalid extension for file "%s". Only "%s" files are supported.', 'tip'),
                $this->file->getClientOriginalName(),
                implode(', ', $this->allowFileExtensions)
            );

            return false;
        }

        try {
            return $this->file->move(
                $this->directory(),
                $this->getTargetName()
            );
        } catch (\Exception $e) {
            $this->errors[] = $e->getMessage();

            return false;
        }
    }

    /**
     *
     * @return UploadedFile
     */
    public function file()
    {
        return $this->file;
    }

    /**
     * 文件后缀
     *
     * @return string
     */
    public function guessClientExtension()
    {
        return $this->clientExtension ?: ($this->clientExtension = $this->file->guessClientExtension());
    }

    /**
     * 使用上传文件名保存文件
     *
     * @return $this
     */
    public function useOriginalName()
    {
        $this->targetName = $this->file->getClientOriginalName();

        return $this;
    }

    /**
     *
     * @param $name
     * @return $this
     */
    public function setTargetName($name)
    {
        $this->targetName = $name;

        return $this;
    }

    /**
     * 文件保存名称
     *
     * @return string
     */
    public function getTargetName()
    {
        return $this->targetName ?: ($this->targetName = $this->generateUniqueFileName());
    }

    /**
     *
     * @return string
     */
    public function getFormatTarget()
    {
        if ($this->dateFormat) {
            return date($this->dateFormat) . '/' . $this->getTargetName();
        }

        return $this->getTargetName();
    }

    /**
     * Generate a unique name for uploaded file.
     *
     * @param UploadedFile $file
     *
     * @return string
     */
    protected function generateUniqueFileName()
    {
        return md5(uniqid()).'.'.$this->guessClientExtension();
    }
}
