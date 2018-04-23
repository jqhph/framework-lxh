<?php

namespace Lxh\Migration\Database;

use Lxh\Migration\Database\Column\Biginteger;
use Lxh\Migration\Database\Column\Binary;
use Lxh\Migration\Database\Column\Blob;
use Lxh\Migration\Database\Column\BooleanColumn;
use Lxh\Migration\Database\Column\Char;
use Lxh\Migration\Database\Column\Cidr;
use Lxh\Migration\Database\Column\Column;
use Lxh\Migration\Database\Column\Date;
use Lxh\Migration\Database\Column\Datetime;
use Lxh\Migration\Database\Column\Decimal;
use Lxh\Migration\Database\Column\Enum;
use Lxh\Migration\Database\Column\Filestream;
use Lxh\Migration\Database\Column\FloatColumn;
use Lxh\Migration\Database\Column\Geometry;
use Lxh\Migration\Database\Column\Inet;
use Lxh\Migration\Database\Column\IntegerColumn;
use Lxh\Migration\Database\Column\Interval;
use Lxh\Migration\Database\Column\Json;
use Lxh\Migration\Database\Column\Jsonb;
use Lxh\Migration\Database\Column\Linestring;
use Lxh\Migration\Database\Column\Macaddr;
use Lxh\Migration\Database\Column\Point;
use Lxh\Migration\Database\Column\Polygon;
use Lxh\Migration\Database\Column\Set;
use Lxh\Migration\Database\Column\StringColumn;
use Lxh\Migration\Database\Column\Text;
use Lxh\Migration\Database\Column\Time;
use Lxh\Migration\Database\Column\Timestamp;
use Lxh\Migration\Database\Column\Uuid;
use Lxh\Migration\Database\Column\Varbinary;
use Lxh\Migration\Exceptions\UnknownColumnException;
use Phinx\Db\Table as PhinxTable;

/**
 *
 * @method \Lxh\Migration\Database\Column\StringColumn string($column)
 * @method \Lxh\Migration\Database\Column\Char char($column)
 * @method \Lxh\Migration\Database\Column\Text text($column)
 * @method \Lxh\Migration\Database\Column\Integer integer($column)
 * @method \Lxh\Migration\Database\Column\Biginteger biginteger($column)
 * @method \Lxh\Migration\Database\Column\FloatColumn float($column)
 * @method \Lxh\Migration\Database\Column\Decimal decimal($column)
 * @method \Lxh\Migration\Database\Column\Datetime datetime($column)
 * @method \Lxh\Migration\Database\Column\Timestamp timestamp($column)
 * @method \Lxh\Migration\Database\Column\Time time($column)
 * @method \Lxh\Migration\Database\Column\Date date($column)
 * @method \Lxh\Migration\Database\Column\Binary binary($column)
 * @method \Lxh\Migration\Database\Column\Varbinary varbinary($column)
 * @method \Lxh\Migration\Database\Column\Blob blob($column)
 * @method \Lxh\Migration\Database\Column\Boolean boolean($column)
 * @method \Lxh\Migration\Database\Column\Json json($column)
 * @method \Lxh\Migration\Database\Column\Jsonb jsonb($column)
 * @method \Lxh\Migration\Database\Column\Uuid uuid($column)
 * @method \Lxh\Migration\Database\Column\Filestream filestream($column)
 * @method \Lxh\Migration\Database\Column\Geometry geometry($column)
 * @method \Lxh\Migration\Database\Column\Point point($column)
 * @method \Lxh\Migration\Database\Column\Linestring linestring($column)
 * @method \Lxh\Migration\Database\Column\Polygon polygon($column)
 * @method \Lxh\Migration\Database\Column\Enum enum($column)
 * @method \Lxh\Migration\Database\Column\Set set($column)
 * @method \Lxh\Migration\Database\Column\Cidr cidr($column)
 * @method \Lxh\Migration\Database\Column\Inet inet($column)
 * @method \Lxh\Migration\Database\Column\Macaddr macaddr($column)
 * @method \Lxh\Migration\Database\Column\Interval Interval($column)
 */
class ChangeColumn
{
    /**
     * @var TableHelper
     */
    protected $table;

    /**
     * @var array
     */
    protected $columntypes = [];

    /**
     * @var array
     */
    protected $columns = [];

    public function __construct(TableHelper $table)
    {
        $this->table = $table;

        $this->columntypes = $table->getColumntypes();
    }

    /**
     * @param $column
     * @param $name
     * @return Column
     * @throws UnknownColumnException
     */
    protected function createColumn($column, $name)
    {
        $class = $this->columntypes[$column];

        return new $class($name);
    }

    /**
     * 增加字段
     *
     * @param Column $column
     * @return $this
     */
    protected function pushColumn(Column $column)
    {
        $this->columns[] = $column;

        return $this;
    }

    /**
     * 完成字段配置
     *
     * @return $this
     */
    public function done()
    {
        foreach ($this->columns as $column) {
            $this->table->phinx()->changeColumn(
                $column->getName(), $column->getType(), $column->getOptions()
            );
        }

        $this->columns = [];

        return $this;
    }

    public function __call($method, $arguments)
    {
        if (!isset($this->columntypes[$method])) {
            throw new UnknownColumnException;
        }
        $name = get_value($arguments, 0);
        if (! $name) {
            throw new \InvalidArgumentException('The column name cannot be empty.');
        }

        $column = $this->createColumn($method, $name);

        $this->pushColumn($column);

        return $column;

    }
}
