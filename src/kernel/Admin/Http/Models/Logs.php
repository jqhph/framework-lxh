<?php

namespace Lxh\Admin\Http\Models;

use Lxh\Auth\Database\Admin;
use Lxh\Auth\Database\Models;
use Lxh\MVC\Model;
use Lxh\Support\Collection;

class Logs extends Model
{
    /**
     * @var string
     */
    protected $tableName = 'admin_operation_log';

    /**
     * @param array $where
     * @param string $order
     * @param int $offset
     * @param int $limit
     */
    public function findList(array $where, $order = 'id DESC', $offset = 0, $limit = 20)
    {
        $data = parent::findList($where, $order, $offset, $limit);

        $adminIds = (new Collection($data))->pluck('admin_id')->all();

        if (!$adminIds) {
            return $data;
        }

        $adminModel = new Admin();

        $adminKeyName = $adminModel->getKeyName();
        $admins = new Collection($adminModel->whereInIds($adminIds)->find());
        $admins = $admins->keyBy($adminKeyName);

        foreach ($data as &$row) {
            $admins = $admins->get($row['admin_id']);
            $row['admin_name'] = get_value($admins, 'username');
        }

        return $data;
    }
}
