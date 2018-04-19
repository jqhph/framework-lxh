<?php

namespace Lxh\Migration;

use Lxh\Migration\Database\Table;
use Phinx\Migration\AbstractMigration;

class Migrator extends AbstractMigration
{
    public function createTable($tableName, \Closure $callback)
    {
        $table = new Table($this->table($tableName));

        return $table;
    }
}
