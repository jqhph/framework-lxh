<?php

namespace Lxh\Migration\Database;

use Lxh\Migration\Database\Column\Column;
use Lxh\Migration\Exceptions\UnknownColumnException;
use Phinx\Db\Table as PhinxTable;

class Table
{
    /**
     * @var PhinxTable
     */
    protected $table;

    protected $columntypes = [
        
    ];

    public function __construct(PhinxTable $table = null)
    {
        $this->table = $table ?: (new PhinxTable(''));
    }

    /**
     * 增加字段
     *
     * @param Column $column
     * @return $this
     */
    public function addColumn(Column $column)
    {
        $this->table->addColumn($column->getName(), $column->getType(), $column->getOptions());

        return $this;
    }

    /**
     * @param $column
     * @param $name
     * @return Column
     * @throws UnknownColumnException
     */
    protected function createColumn($column, $name)
    {
        if (! isset($this->columntypes[$column])) {
            throw new UnknownColumnException;
        }

        $class = $this->columntypes[$column];

        return new $class($name);
    }

    /**
     * @return PhinxTable
     */
    public function phinx()
    {
        return $this->table;
    }

    public function __call($method, $arguments)
    {
        if (!isset($this->columntypes[$method])) {
            return call_user_func_array([$this->table, $method], $arguments);
        }
        $name = get_value($arguments, 0);
        if (! $name) {
            throw new \InvalidArgumentException('The column name cannot be empty.');
        }

        $column = $this->createColumn($method, $name);

        $this->addColumn($column);

        return $column;

    }
}