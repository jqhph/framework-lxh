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
 * @method \Lxh\Migration\Database\Column\StringColumn  string($column)
 * @method \Lxh\Migration\Database\Column\Char          char($column)
 * @method \Lxh\Migration\Database\Column\Text          text($column)
 * @method \Lxh\Migration\Database\Column\IntegerColumn integer($column)
 * @method \Lxh\Migration\Database\Column\Biginteger    biginteger($column)
 * @method \Lxh\Migration\Database\Column\FloatColumn   float($column)
 * @method \Lxh\Migration\Database\Column\Decimal       decimal($column)
 * @method \Lxh\Migration\Database\Column\Datetime      datetime($column)
 * @method \Lxh\Migration\Database\Column\Timestamp     timestamp($column)
 * @method \Lxh\Migration\Database\Column\Time          time($column)
 * @method \Lxh\Migration\Database\Column\Date          date($column)
 * @method \Lxh\Migration\Database\Column\Binary        binary($column)
 * @method \Lxh\Migration\Database\Column\Varbinary     varbinary($column)
 * @method \Lxh\Migration\Database\Column\Blob          blob($column)
 * @method \Lxh\Migration\Database\Column\BooleanColumn boolean($column)
 * @method \Lxh\Migration\Database\Column\Json          json($column)
 * @method \Lxh\Migration\Database\Column\Jsonb         jsonb($column)
 * @method \Lxh\Migration\Database\Column\Uuid          uuid($column)
 * @method \Lxh\Migration\Database\Column\Filestream    filestream($column)
 * @method \Lxh\Migration\Database\Column\Geometry      geometry($column)
 * @method \Lxh\Migration\Database\Column\Point         point($column)
 * @method \Lxh\Migration\Database\Column\Linestring    linestring($column)
 * @method \Lxh\Migration\Database\Column\Polygon       polygon($column)
 * @method \Lxh\Migration\Database\Column\Enum          enum($column)
 * @method \Lxh\Migration\Database\Column\Set           set($column)
 * @method \Lxh\Migration\Database\Column\Cidr          cidr($column)
 * @method \Lxh\Migration\Database\Column\Inet          inet($column)
 * @method \Lxh\Migration\Database\Column\Macaddr       macaddr($column)
 * @method \Lxh\Migration\Database\Column\Interval      Interval($column)
 */
class TableHelper
{
    /**
     * @var PhinxTable
     */
    protected $table;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var array
     */
    protected static $columntypes = [
        'string'     => StringColumn::class,
        'char'       => Char::class,
        'text'       => Text::class,
        'integer'    => IntegerColumn::class,
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
        'boolean'    => BooleanColumn::class,
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

    /**
     * @var array
     */
    protected $columns = [];

    /**
     * @var array
     */
    protected $indexes = [];

    public function __construct(PhinxTable $table = null)
    {
        $this->table = $table ?: (new PhinxTable(''));
    }

    /**
     * 指定主键字段
     * 单独设置此字段并不会开启自增功能
     *
     * @param string|array $names 字段名称
     * @return $this
     */
    public function primaryKey($names)
    {
        return $this->id(false)->setOption('primary_key', $names);
    }

    /**
     * 主键是否是非负数
     *
     * @param bool $flag
     * @return TableHelper
     */
    public function signed($flag = false)
    {
        return $this->setOption('signed', $flag);
    }

    /**
     * @return $this
     */
    public function unsigned()
    {
        return $this->setOption('signed', false);
    }

    /**
     * 手动关闭默认增加的自增主键
     * 或改变自增主键字段名
     *
     * @param string|false $value false关闭默认增加的自增主键， 字符串则是更改自增主键字段名
     * @return $this
     */
    public function id($value)
    {
        return $this->setOption('id', $value);
    }

    /**
     * 设置表注释
     * Mysql支持
     *
     * @param string $comment
     * @return $this
     */
    public function comment($comment)
    {
        return $this->setOption('comment', $comment);
    }

    /**
     * 使用utf8mb4编码
     * 默认utf8
     *
     * @return TableHelper
     */
    public function utf8mb4()
    {
        return $this->collation('utf8mb4_general_ci');
    }

    /**
     * 定义表的语言（默认 utf8-general-ci）
     * Mysql支持
     *
     * @param $z
     * @return $this
     */
    public function collation($z)
    {
        return $this->setOption('collation', $z);
    }

    /**
     * 设置表引擎为INNODB
     *
     * @return $this
     */
    public function innodb()
    {
        return $this->engine('InnoDB');
    }

    /**
     * 设置表以前为MYISAM
     *
     * @return $this
     */ 
    public function myisam()
    {
        return $this->engine('MyISAM');
    }

    /**
     * 设置表引擎，默认 InnoDB
     * Mysql支持
     *
     * @param $engine
     * @return $this
     */
    public function engine($engine)
    {
        return $this->setOption('engine', $engine);
    }

    /**
     * 设置选项值
     *
     * @param $key
     * @param $value
     * @return $this
     */
    public function setOption($key, $value)
    {
        $this->options[$key] = &$value;

        return $this;
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
     * @param $name
     * @param null $type
     * @param $options
     * @return $this
     */
    public function addColumn($name, $type = null, $options)
    {
        $this->table->addColumn($name, $type, $options);

        return $this;
    }

    /**
     * 更改字段
     *
     * @param \Closure $call
     * @return $this
     */
    public function changeColumn(\Closure $call)
    {
        $change = new ChangeColumn($this);

        $call($change);

        $change->done();

        return $this;
    }

    /**
     * 添加索引
     *
     * @param $fields
     * @return Index
     */
    public function addIndex($fields)
    {
        return $this->indexes[] = new Index($this, $fields);
    }

    /**
     * @param $column
     * @param $name
     * @return Column
     * @throws UnknownColumnException
     */
    protected function createColumn($column, $name)
    {
        if (! isset(static::$columntypes[$column])) {
            throw new UnknownColumnException;
        }

        $class = static::$columntypes[$column];

        return new $class($name);
    }

    /**
     * @return PhinxTable
     */
    public function phinx()
    {
        return $this->table;
    }

    /**
     * Does the table exist?
     *
     * @return bool
     */
    public function exists()
    {
        return $this->table->exists();
    }

    /**
     * Drops the database table.
     *
     * @return void
     */
    public function drop()
    {
        $this->done();
        $this->table->drop();
    }

    /**
     * Remove a table column.
     *
     * @param string $columnName Column Name
     * @return $this
     */
    public function removeColumn($columnName)
    {
        $this->table->removeColumn($columnName);

        return $this;
    }

    /**
     * Rename a table column.
     *
     * @param string $oldName Old Column Name
     * @param string $newName New Column Name
     * @return $this
     */
    public function renameColumn($oldName, $newName)
    {
        $this->table->renameColumn($oldName, $newName);

        return $this;
    }

    /**
     * Renames the database table.
     *
     * @param string $newTableName New Table Name
     * @return $this
     */
    public function rename($newTableName)
    {
        $this->table->rename($newTableName);

        return $this;
    }

    /**
     * 不需要调用save方法，索引会立即删除
     *
     * @param $fields
     * @return $this
     */
    public function removeIndex($fields)
    {
        $this->table->removeIndex($fields);
        return $this;
    }

    /**
     * 不需要调用save方法，索引会立即删除
     *
     * @param $name
     * @return $this
     */
    public function removeIndexByName($name)
    {
        $this->table->removeIndexByName($name);
        return $this;
    }

    public function addForeignKey($columns, $referencedTable, $referencedColumns = ['id'], $options = [])
    {
        $this->table->addForeignKey($columns, $referencedTable, $referencedColumns, $options);

        return $this;
    }

    public function dropForeignKey($columns, $constraint = null)
    {
        $this->table->dropForeignKey($columns, $constraint);

        return $this;
    }

    public function save()
    {
        $this->done();
        $this->table->save();
    }

    public function create()
    {
        $this->done();
        $this->table->create();
    }

    public function saveData()
    {
        $this->done();
        $this->table->saveData();
    }

    public function update()
    {
        $this->done();
        $this->table->update();
    }

    /**
     * 完成字段配置
     *
     * @return $this
     */
    public function done()
    {
        $this->table->setOptions($this->options);

        foreach ($this->columns as $column) {
            $this->table->addColumn($column->getName(), $column->getType(), $column->getOptions());
        }

        foreach ($this->indexes as $index) {
            $this->table->addIndex($index->getColumns(), $index->getOptions());
        }

        $this->columns = $this->indexes = [];

        return $this;
    }

    public function getColumntypes()
    {
        return static::$columntypes;
    }

    public function __call($method, $arguments)
    {
        if (!isset(static::$columntypes[$method])) {
            return call_user_func_array([$this->table, $method], $arguments);
        }
        $name = getvalue($arguments, 0);
        if (! $name) {
            throw new \InvalidArgumentException('The column name cannot be empty.');
        }

        $column = $this->createColumn($method, $name);

        $this->pushColumn($column);

        return $column;

    }
}