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
    const BATCH_DELETE = 'batch-delete';
    const BATCH_UPDATE = 'batch-update';
    const BATCH_CREATE = 'batch-create';
    const TRASH = 'trash';
    const RESTORE = 'restore';
    const DELETE_PERMANENTLY = 'delete-permanently';
    const BATCH_RESTORE = 'batch-restore';
    const BATCH_DELETE_PERMANENTLY = 'batch-delete-permanently';

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
            '.' . static::BATCH_DELETE,
            '.' . static::BATCH_CREATE,
            '.' . static::BATCH_UPDATE,
            '.' . static::TRASH,
            '.' . static::RESTORE,
            '.' . static::DELETE_PERMANENTLY,
            '.' . static::BATCH_RESTORE,
            '.' . static::BATCH_DELETE_PERMANENTLY,
        ];
    }
}
