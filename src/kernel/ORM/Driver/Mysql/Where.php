<?php

namespace Lxh\ORM\Driver\Mysql;

use Lxh\Exceptions\InvalidArgumentException;

trait Where
{
    /**
     * @var array
     */
    protected $orWhereData = [];

    /**
     * @var array
     */
    protected $wheres = [];

    /**
     * @var array
     */
    protected $whereData = [];

    /**
     * @var array
     */
    protected $orWheres = [];

    public function where(& $p1, $p2 = '=', $p3 = null, $table = null)
    {
        $tb = $table ? $table : $this->tableName;
        if (! $tb) {
            throw new InvalidArgumentException('表名不能为空');
        }

        $content = $this->whereBuilder->table($tb)->build($p1, $p2, $p3);

        $this->wheres = array_merge($this->wheres, $content['where']);
        $this->whereData = array_merge($this->whereData, $content['params']);

        return $this;
    }

    /**
     * @param $whereString
     * @param array $prepareData
     */
    public function whereRaw($whereString, array $prepareData = [])
    {
        $this->wheres[] = &$whereString;
        if ($prepareData) {
            $this->whereData = array_merge($this->whereData, $prepareData);
        }

        return $this;
    }

    /**
     * @param $p1
     * @param string $p2
     * @param null $p3
     * @param null $table
     * @return $this
     */
    public function orWhere(& $p1, $p2 = '=', $p3 = null, $table = null)
    {
        $tb = $table ? $table : $this->tableName;

        $content = $this->whereBuilder->table($tb)->build($p1, $p2, $p3);

        $this->orWheres[] = '(' . implode(' AND ', $content['where']) . ')';
        $this->whereData = array_merge($this->whereData, $content['params']);

        return $this;
    }
}
