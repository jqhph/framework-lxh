<?php

namespace Lxh\OperationsLogger\Entities;

use Lxh\Admin\Http\Models\Logs;
use Lxh\MVC\Model;

/**
 * 后台用户操作日志
 *
 */
class AdminAction extends Entity
{
    // 0其他，1新增，2修改，3删除',
    const INSERT              = 1;
    const UPDATE              = 2;
    const DELETE              = 3;
    const BATCH_UPDATE        = 4;
    const BATCH_DELETE        = 5;
    const MOVE_TO_TRASH       = 6;
    const BATCH_MOVE_TO_TRASH = 7;
    const RESTORE             = 8;
    const OTHOR               = 0;

    public $id;
    public $adminId;
    public $path;
    public $method;
    public $ip;
    public $input;
    public $createdAt;
    public $table;
    public $type = 0;

    /**
     * @var Logs
     */
    protected $model;

    // 1 GET, 2POST, 3PUT, 4DELETE, 5OPTION
    protected $methods = [
        'GET'    => 1,
        'POST'   => 2,
        'PUT'    => 3,
        'DELETE' => 4,
        'OPTION' => 5,
        'HEAD'   => 6,
    ];
    
    public function __construct(Model $model = null)
    {
        parent::__construct($model);

        $this->model = new Logs();
    }

    /**
     *
     * @return array
     */
    public static function types()
    {
        return [
            static::INSERT              => trans('insert'),
            static::UPDATE              => trans('update'),
            static::DELETE              => trans('delete'),
            static::BATCH_DELETE        => trans('batch delete'),
            static::RESTORE             => trans('restore'),
            static::MOVE_TO_TRASH       => trans('move to trash'),
            static::BATCH_MOVE_TO_TRASH => trans('batch move to trash'),
        ];
    }

    /**
     *
     * @return $this
     */
    public function setInsert()
    {
        $this->type = static::INSERT;
        return $this;
    }

    /**
     *
     * @return $this
     */
    public function setUpdate()
    {
        $this->type = static::UPDATE;
        return $this;
    }

    /**
     *
     * @return $this
     */
    public function setMoveToTrash()
    {
        $this->type = static::MOVE_TO_TRASH;
        return $this;
    }

    /**
     *
     * @return $this
     */
    public function setBatchMoveToTrash()
    {
        $this->type = static::BATCH_MOVE_TO_TRASH;
        return $this;
    }

    /**
     *
     * @return $this
     */
    public function setRestore()
    {
        $this->type = static::RESTORE;
        return $this;
    }

    /**
     *
     * @return $this
     */
    public function setDelete()
    {
        $this->type = static::DELETE;
        return $this;
    }

    public function setBatchUpdate()
    {
        $this->type = static::BATCH_UPDATE;
        return $this;
    }

    public function setBatchDelete()
    {
        $this->type = static::BATCH_DELETE;
        return $this;
    }

    public function toArray()
    {
        if (! $this->enable) return [];

        $req = request();

        (! $this->path)      && ($this->path = $req->getUri()->getPath());
        (! $this->method)    && ($this->method = get_value($this->methods, $req->getMethod(), 0));
        (! $this->ip)        && ($this->ip = ip2long($req->ip()));
        (! $this->adminId)   && ($this->adminId = __admin__()->getId());
        (! $this->createdAt) && ($this->createdAt = time());

        if ($this->entityModel) {
            (! $this->table) && ($this->table = $this->entityModel->getTableName());
            (! $this->input) && ($this->input = $this->entityModel->toJson());
        }

        $attrs = [
             'id'         => &$this->id,
             'admin_id'   => &$this->adminId,
             'path'       => &$this->path,
             'method'     => &$this->method,
             'ip'         => &$this->ip,
             'input'      => &$this->input,
             'created_at' => &$this->createdAt,
             'table'      => &$this->table,
             'type'       => &$this->type,
        ] ;

        if (! $attrs['id']) unset($attrs['id']);

        return $attrs;
    }

    public function add()
    {
        return $this->enable ? $this->model->attach($this->toArray())->add() : false;
    }

}
