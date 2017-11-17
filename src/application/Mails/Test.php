<?php

namespace Lxh\Mails;

use Lxh\Mail\Mailable;

class Test extends Mailable
{
    public function build()
    {
        $this->from('example@example.com')
            ->subject('测试邮件')
            ->with('test', 'hehe')
            ->view('emails.test');
    }
}
