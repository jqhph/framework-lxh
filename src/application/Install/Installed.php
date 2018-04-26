<?php

namespace Lxh\Install;

use Lxh\Admin\Layout\Row;
use Lxh\Admin\Widgets\Card;

trait Installed
{
    /**
     * 判断是否已安装
     *
     * @return bool
     */
    protected function isinstalled()
    {
        if (is_file(__DIR__.'/installed.tmp')) {
            return true;
        }

        return false;
    }

    protected function setisinstalled()
    {
        return files()->putContents(__DIR__.'/installed.tmp', '');
    }

    protected function alreadyInstalled()
    {
        $this->content->row(function (Row $row) {
            $tip    = trans('You appear to have already installed Lxh Framework. To reinstall please clear your old database tables first.');
            $btn    = trans('Sign in');
            $prefix = config('admin.route-prefix');

            $card = new Card(
                trans('Already Installed'),
                "<p>$tip</p>
                <br>
                <a href='/$prefix/login' class='btn btn-primary'>&nbsp;&nbsp;&nbsp;&nbsp;$btn&nbsp;&nbsp;&nbsp;&nbsp;</a>
"
            );

            $row->column(12, view('install::content', ['card' => $card])->render());
        });

        return $this->content->render();
    }
}
