<?php

namespace Lxh\OperationsLogger\Entities;

use Lxh\Admin\Http\Models\Logs;

/**
 * 后台用户操作日志
 *
 */
class AdminAction extends Entity
{
    // 0其他，1新增，2修改，3删除',
    const INSERT = 1;
    const UPDATE = 2;
    const DELETE = 3;
    const OTHOR  = 0;

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
    
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->model = new Logs();
    }

    public function setInsert()
    {
        $this->type = static::INSERT;
        return $this;
    }

    public function setUpdate()
    {
        $this->type = static::UPDATE;
        return $this;
    }

    public function setDelete()
    {
        $this->type = static::DELETE;
        return $this;
    }

    public function toArray()
    {
        $req = request();

        (! $this->path)      && ($this->path = $req->getUri()->getPath());
        (! $this->method)    && ($this->method = get_value($this->methods, $req->getMethod(), 0));
        (! $this->ip)        && ($this->ip = ip2long($req->ip()));
        (! $this->adminId)   && ($this->adminId = __admin__()->getId());
        (! $this->createdAt) && ($this->createdAt = time());

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
        return $this->model->attach($this->toArray())->add();
    }

}
