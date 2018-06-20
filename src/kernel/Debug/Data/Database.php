<?php
/**
 * Created by PhpStorm.
 * User: Jqh
 * Date: 2017/6/24
 * Time: 11:34
 */

namespace Lxh\Debug\Data;

use Lxh\Helper\Entity;
use Lxh\ORM\Connect\PDO;

class Database extends Record
{
    public function last()
    {
        return [
            'command' => & PDO::$lastCommand,
            'params'  => & PDO::$lastPrepareData,
        ];
    }

}
