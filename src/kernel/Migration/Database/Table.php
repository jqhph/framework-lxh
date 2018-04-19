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
class Table
{
    /**
     * @var PhinxTable
     */
    protected $table;

    /**
     * @var array
     */
    protected $columntypes = [
        'string'     => StringColumn::class,
        'char'       => Char::class,
        'text'       => Text::class,
        'integer'    => Integer::class,
        'biginteger' => Biginteger::class,
        'float'      => FloatColumn::class,
        'decimal'    => Decimal::class,
        'datetime'   => Datetime::class,
        'timestamp'  => Timestamp::class,
        'time'       => Time::class,
        'date'       => Date::class,
        'binary'     => Binary::class,
        'varbinary'  => Varbinary::class,
        'blob'       => Blob::class,
        'boolean'    => Boolean::class,
        'json'       => Json::class,
        'jsonb'      => Jsonb::class,
        'uuid'       => Uuid::class,
        'filestream' => Filestream::class,

        // Geospatial database types
        'geometry'   => Geometry::class,
        'point'      => Point::class,
        'linestring' => Linestring::class,
        'polygon'    => Polygon::class,

        // only for mysql so far
        'enum' => Enum::class,
        'set'  => Set::class,

        // only for postgresql so far
        'cidr'     => Cidr::class,
        'inet'     => Inet::class,
        'macaddr'  => Macaddr::class,
        'interval' => Interval::class,

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