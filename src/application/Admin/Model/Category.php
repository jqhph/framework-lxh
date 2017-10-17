<?php
/**
 *
 * @author Jqh
 * @date   2017-10-17 19:37:45
 */

namespace Lxh\Admin\Model;

use Lxh\MVC\Model;
use Lxh\Contracts\Container\Container;

class Category extends Model
{
    protected $selectFields = ['id', 'name', 'desc', 'created_at', 'modified_at', 'admin.username AS created_by'];

    protected $admin;

    public function __construct($name, Container $container)
    {
        parent::__construct($name, $container);

        $this->admin = admin();

    }

    public function beforeSave($id, array & $data)
    {
        $data['modified_at'] = time();
    }

    public function beforeAdd(array & $data)
    {
        $data['created_at']    = time();
        $data['created_by_id'] = $this->admin->id;
    }

    /**
     * 获取列表页数据
     *
     * @param  array | string $where
     * @param  int $offset
     * @param  int $maxSize
     * @param  string $orderString
     * @return array
     */
    public function findList(array $where, $orderString = 'id Desc', $offset = 0, $maxSize = 20)
    {
        $q = $this->query()
            ->select($this->selectFields)
            ->leftJoin('admin', 'admin.id', 'created_by_id')
            ->limit($offset, $maxSize);

        if ($where) {
            $q->where($where);
        }

        if ($orderString) {
            $q->sort($orderString);
        }

        return $q->find();
    }

    // 查找数据
    public function find()
    {
        $id = $this->{$this->idFieldsName};

        if ($id) {
            $data = $this->query()->select($this->selectFields)->leftJoin('admin', 'admin.id', 'created_by_id')->where($this->idFieldsName, $id)->findOne();
            $this->fill($data);
            return $data;
        }
        return $this->query()->select($this->selectFields)->leftJoin('admin', 'admin.id', 'created_by_id')->where('deleted', 0)->find();
    }
}
