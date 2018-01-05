<?php

namespace Lxh\Auth\Database;

use Lxh\MVC\Model;

class Role extends Model
{
    use Concerns\IsRole;


    protected function initialize()
    {
        $this->tableName = Models::table('roles');
    }
}
