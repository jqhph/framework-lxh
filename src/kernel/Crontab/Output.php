<?php

namespace Lxh\Crontab;

use Lxh\Helper\Util;

class Output
{

    public function __construct()
    {
    }

    public function newline($count = 1)
    {
        echo str_repeat("\n", $count);
    }

    public function line($msg, $count = 1)
    {
        if (is_array($msg)) {
            echo json_encode($msg, true);
        } else {
            echo $msg;
        }

        $this->newline($count);
    }

}
