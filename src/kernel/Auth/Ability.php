<?php

namespace Lxh\Auth;

use Lxh\Admin\MVC\Model;
use Lxh\Auth\Cache\Store;
use Lxh\Auth\Clipboard;
use Lxh\Auth\Database\Models;
use Lxh\Support\Collection;

class Ability
{
    /**
     * 公共权限名称定义
     */
    const READ = 'read';
    const CREATE = 'add';
    const UPDATE = 'edit';
    const DELETE = 'delete';
    const BATCHDELETE = 'batch-delete';
    const EXPORT = 'export';
    const IMPORT = 'import';

}
