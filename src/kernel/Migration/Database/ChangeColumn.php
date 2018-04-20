<?php

namespace Lxh\Migration\Database;

use Lxh\Migration\Database\Column\Biginteger;
use Lxh\Migration\Database\Column\Binary;
use Lxh\Migration\Database\Column\Blob;
use Lxh\Migration\Database\Column\Boolean;
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
use Lxh\Migration\Database\Column\Integer;
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
 * @method StringColumn string($column)
 * @method Char char($column)
 * @method Text text($column)
 * @method Integer integer($column)
 * @method Biginteger biginteger($column)
 * @method FloatColumn float($column)
 * @method Decimal decimal($column)
 * @method Datetime datetime($column)
 * @method Timestamp timestamp($column)
 * @method Time time($column)
 * @method Date date($column)
 * @method Binary binary($column)
 * @method Varbinary varbinary($column)
 * @method Blob blob($column)
 * @method Boolean boolean($column)
 * @method Json json($column)
 * @method Jsonb jsonb($column)
 * @method Uuid uuid($column)
 * @method Filestream filestream($column)
 * @method Geometry geometry($column)
 * @method Point point($column)
 * @method Linestring linestring($column)
 * @method Polygon polygon($column)
 * @method Enum enum($column)
 * @method Set set($column)
 * @method Cidr cidr($column)
 * @method Inet inet($column)
 * @method Macaddr macaddr($column)
 * @method Interval Interval($column)
 */
class ChangeColumn
{
    /**
     * @var Table
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

    public function __construct(Table $table)
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
