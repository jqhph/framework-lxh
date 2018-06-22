<?php

namespace Lxh\Tests\Exp;

use Lxh\Tests\Annotations\Test;

class User
{
    /**
     * @Test(pro1=123)
     */
    public function exec()
    {
        echo 'user';
    }
}
