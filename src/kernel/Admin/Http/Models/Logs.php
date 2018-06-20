<?php

namespace Lxh\Admin\Http\Models;

use Lxh\Auth\Database\Admin;
use Lxh\Auth\Database\Models;
use Lxh\Mvc\Model;
use Lxh\Support\Collection;

class Logs extends Model
{
    /**
     * @var string
     */
    protected $tableName = 'admin_operation_log';

}
