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
    const CREATE = 'create';
    const DETAIL = 'detail';
    const UPDATE = 'update';
    const DELETE = 'delete';
    const EXPORT = 'export';
    const IMPORT = 'import';
    const UPLOAD = 'upload';
    const BATCHDELETE = 'batch-delete';
    const BATCHUPDATE = 'batch-update';
    const BATCHCREATE = 'batch-create';
    const TRASH = 'trash';
    const RESTORE = 'restore';
    const DELETEPERMANENTLY = 'delete-permanently';
    const BATCHRESTORE = 'batch-restore';
    const BATCHDELETEPERMANENTLY = 'batch-delete-permanently';

    public static function getAbilitiesSupport()
    {
        return [
            '.' . static::READ,
            '.' . static::CREATE,
            '.' . static::DETAIL,
            '.' . static::UPDATE,
            '.' . static::DELETE,
            '.' . static::EXPORT,
            '.' . static::IMPORT,
            '.' . static::UPLOAD,
            '.' . static::BATCHDELETE,
            '.' . static::BATCHCREATE,
            '.' . static::BATCHUPDATE,
            '.' . static::TRASH,
            '.' . static::RESTORE,
            '.' . static::DELETEPERMANENTLY,
            '.' . static::BATCHRESTORE,
            '.' . static::BATCHDELETEPERMANENTLY,
        ];
    }
}
