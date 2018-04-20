<?php

namespace Lxh\Migration;

use Lxh\Migration\Database\Table;
use Phinx\Migration\AbstractMigration;

class Migrator extends AbstractMigration
{
    /**
     * @param string $tableName
     * @param \Closure $callback
     * @return $this
     */
    public function makeTable($tableName, $callback = null)
    {
        $table = new Table($this->table($tableName));

        $callback($table);

        $table->done();

        return $this;
    }
}
