<?php

namespace Lxh\Migration;

use Lxh\Migration\Database\Table;
use Phinx\Migration\AbstractMigration;

class Migrator extends AbstractMigration
{
    /**
     * @param string $tableName
     * @param \Closure $callback
     * @return Table
     */
    public function createTable($tableName, \Closure $callback = null)
    {
        $table = new Table($this->table($tableName));

        $callback && $callback($table);

        return $table;
    }
}
