<?php

namespace Lxh\ORM\Driver\Mysql;

trait Join
{
    /**
     * @var array
     */
    protected $join = [];

    /**
     * 多对多关联(不支持AS别名)
     * 主键字段必须命名为 id
     *
     * @param $mid string 中间表表名
     * @param $relate string 要关联的表
     * @return $this
     */
    public function manyToMany($mid, $relate)
    {
        $tmp = "`$mid`";
        return $this->leftJoin($mid, "$tmp.{$this->tableName}_id", 'id')
            ->leftJoin($relate, "$tmp.{$relate}_id", "`$relate`.id");
    }

    /**
     * 多对多只关联中间表的情况
     * 主键字段必须命名为 id
     *
     * @date   2016-11-9 下午1:14:31
     * @author jqh
     * @param  string $mid 中间表
     * @param  string $as  别名
     * @return $this
     */
    public function relateMany($mid, $as = null)
    {
        if (! $as) {
            $as = "`$mid`";
        }
        return $this->leftJoin($mid, "$as.{$this->tableName}_id", 'id');
    }

    /**
     * 主键字段必须命名为 id
     *
     * @param $table
     * @param null $as
     * @param null $table2
     * @return $this
     */
    public function belongsTo($table, $as = null, $table2 = null)
    {
        $left = $table;
        if ($as) {
            $as   = "`$as`";
            $left = "`$left` AS $as";
        } else {
            $as = "`$left`";
        }
        if (! $table2) {
            return $this->leftJoin($left, "$as.id", "{$table}_id");
        }

        return $this->leftJoin($left, "$as.id", "`$table2`.{$table}_id");
    }

    /**
     * 主键字段必须命名为 id
     *
     * @param $table
     * @param null $as
     * @param null $table2
     * @return $this
     */
    public function hasOne($table, $as = null, $table2 = null)
    {
        $left = $table;

        if ($as) {
            $as   = "`$as`";
            $left = "`$left` AS $as";
        } else {
            $as = "`$left`";
        }

        if (! $table2) {
            return $this->leftJoin($left, 'id', "$as.{$this->tableName}_id");
        }

        return $this->leftJoin($left, "`$table2`.id", "$as.{$this->tableName}_id");
    }

    /**
     * @param $table
     * @param null $p1
     * @param null $p2
     * @param string $condit
     * @return $this
     */
    public function leftJoin(& $table, $p1 = null, $p2 = null, $condit = '=')
    {
        return $this->join($table, $p1, $p2, $condit, 'LEFT');
    }

    /**
     * @param $table
     * @param null $p1
     * @param null $p2
     * @param string $condit
     * @return $this
     */
    public function rightJoin(& $table, $p1 = null, $p2 = null, $condit = '=')
    {
        return $this->join($table, $p1, $p2, $condit, 'RIGHT');
    }

    /**
     * 必须先调用from方法指定表名ss
     *
     * @param $table
     * @param null $field1
     * @param null $field2
     * @param string $condit
     * @param string $prefix
     * @return $this
     */
    public function join(& $table, $field1 = null, $field2 = null, $condit = '=', $prefix = '')
    {
        if (preg_match($this->varPattern, $table)) {
            $table = "`$table`";
        }

        $p1IsVar = preg_match($this->varPattern, $field1);
        $p2IsVar = preg_match($this->varPattern, $field2);

        if (! $p1IsVar && ! $p2IsVar) {
            $this->join[] = " $prefix JOIN $table ON $field1 $condit $field2";

        } elseif (! $p1IsVar) {
            $this->join[] = " $prefix JOIN $table ON $field1 $condit `{$this->tableName}`.`$field2`";

        } else {
            $this->join[] = " $prefix JOIN $table ON $field2 $condit `{$this->tableName}`.`$field1`";

        }
        return $this;
    }

    /**
     * @return string
     */
    protected function getLeftJoinSql()
    {
        if (count($this->join) > 0) {
            return implode(' ', $this->join);
        }
    }

    /**
     * @param $string
     * @return $this
     */
    public function joinRaw($string)
    {
        $this->join[] = &$string;
        return $this;
    }

}
