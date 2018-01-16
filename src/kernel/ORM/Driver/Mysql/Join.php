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
     * 必须先调用from方法！
     *
     * 表结构如下:
     *  menu 		 --- menu_content_id
     *  menu_content --- id, menu_type_id
     *  menu_type	 --- id
     *
     * 使用示例:
     *
    $q->from('menu')
    ->leftJoin('menu_content AS u', 'u.id', 'menu_content_id')
    ->leftJoin('menu_type AS w', 'u.menu_type_id', 'w.id')
    相当于
    $q->from('menu')
    ->belongTo('menu_content', 'u')
    ->belongTo('menu_type', 'w', 'u')

    SELECT * FROM `menu`
    LEFT JOIN `menu_content` AS `u` ON u.id = `menu`.`menu_content_id`
    LEFT JOIN `menu_type` AS `w` ON w.id = `u`.menu_type_id
     *
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
     * 跟上面belongsTo刚好相反, 必须先调用from方法！
     * 表结构如下:
     *
     * menu_content --- id
     * menu			--- menu_content_id

    $q->from('menu_content')
    ->hasOne('menu')
    ->readRow();

    SELECT *  FROM `menu_content`
    LEFT JOIN `menu` ON `menu`.menu_content_id = menu_content.`id` LIMIT 1
     *
     *
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
     * 传入：
     * $this->leftJoin('menu_content AS u', 'u.id', 'menu_content_id')
    ->leftJoin('wechat_menu_type AS w', 'u.wechat_menu_type_id', 'w.id')
     *
     * 返回：
    LEFT JOIN `menu_content` AS u    ON `table`.`menu_content_id`      = `u`.`id`
    LEFT JOIN `wechat_menu_type` AS w ON `u`.`wechat_menu_type_id` = `w`.`id`
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
