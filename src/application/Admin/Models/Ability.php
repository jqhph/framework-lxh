<?php
/**
 *
 * @author Jqh
 * @date   2018-01-06 10:50:07
 */

namespace Lxh\Admin\Models;

use Lxh\MVC\Model;

class Ability extends \Lxh\Auth\Database\Ability
{
    protected $tableName = 'abilities';

    public function afterUpdate($id, array & $data, $result)
    {
    }

    public function afterDelete($id, $result)
    {
    }

    public function afterAdd($insertId, array & $data)
    {
        if (! $insertId) return;
    }

}
