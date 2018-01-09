<?php
/**
 *
 * @author Jqh
 * @date   2018-01-06 10:50:07
 */

namespace Lxh\Admin\Models;

use Lxh\MVC\Model;

class Ability extends Model
{
    protected $tableName = 'abilities';

    public function beforeUpdate($id, array & $data)
    {
        $data['modified_at'] = time();
    }

    public function afterUpdate($id, array & $data, $result)
    {
    }

    public function afterDelete($id, $result)
    {
    }

    public function beforeAdd(array & $data)
    {
        $data['created_at']    = time();
        $data['created_by_id'] = admin()->id;
    }

    public function afterAdd($insertId, array & $data)
    {
        if (! $insertId) return;
    }

}
