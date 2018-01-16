<?php

namespace Lxh\ORM\Driver\Mysql;

trait Having
{
    /**
     * @var array
     */
    protected $havingData = [];

    /**
     * @var array
     */
    protected $having = [];

    /**
     * @var array
     */
    protected $orHaving = [];

    /**
     * @param $p1
     * @param string $p2
     * @param null $p3
     * @param null $table
     * @return $this
     */
    public function having(& $p1, $p2 = '=', $p3 = null, $table = null)
    {
        $tb = $table ? $table : $this->tableName;

        $content = $this->whereBuilder->table($tb)->build($p1, $p2, $p3);

        $this->having = array_merge($this->having, $content['where']);
        $this->havingData = array_merge($this->havingData, $content['params']);
        return $this;
    }

    /**
     * @param $p1
     * @param string $p2
     * @param null $p3
     * @param null $table
     * @return $this
     */
    public function orHaving($p1, $p2 = '=', $p3 = null, $table = null)
    {
        $tb = $table ? $table : $this->tableName;

        $content = $this->whereBuilder->table($tb)->build($p1, $p2, $p3);

        $this->orHaving = array_merge($this->orHaving, $content['where']);
        $this->havingData = array_merge($this->havingData, $content['params']);
        return $this;
    }

    /**
     * @param $whereString
     * @param array $prepareData
     */
    public function havingRaw($whereString, array $prepareData = [])
    {
        $this->having[] = &$whereString;
        if ($prepareData) {
            $this->havingData = array_merge($this->havingData, $prepareData);
        }

        return $this;
    }
}
