<?php

namespace Lxh\Auth\Database;

use Lxh\Admin\MVC\Model;

class Role extends Model
{
    use Concerns\IsRole;

    /**
     * 权限实体类型
     *
     * @var int
     */
    protected $morphType = 2;


    protected function initialize()
    {
        $this->tableName = Models::table('roles');
    }

    public function getMorphType()
    {
        return $this->morphType;
    }
}
