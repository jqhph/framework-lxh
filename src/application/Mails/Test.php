<?php

namespace Lxh\Mails;

use Lxh\Mail\Mailable;

class Test extends Mailable
{
    public function build()
    {
        $icon = $this->normalizePublicPath(load_img('favicon.ico'));

        $this->subject('测试邮件')
             ->view('emails.test', ['test' => 'haha', 'icon' => $icon]);
    }
}
