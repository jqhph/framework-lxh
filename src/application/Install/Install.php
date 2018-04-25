<?php

namespace Lxh\Install;

class Install
{
    protected $files;

    public function __construct()
    {
        $this->files = files();

    }

    /**
     *
     * @return string
     */
    public function up()
    {
    }

    public function isInstalled()
    {
        return is_file(__DIR__.'/installed');
    }

    public function setIsInstalled()
    {
        return $this->files->putContents(__DIR__.'/installed', '');
    }
}
