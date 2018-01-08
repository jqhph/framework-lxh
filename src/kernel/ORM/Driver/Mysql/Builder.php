<?php

namespace Lxh\ORM\Driver\Mysql;

use Lxh\Exceptions\InternalServerError;
use Lxh\Contracts\Container\Container;
use Lxh\Exceptions\InvalidArgumentException;
use Lxh\ORM\Query;

class Builder
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var string
     */
    protected $tableName;

    /**
     * @var string
     */
    protected $field;

    protected $orWhereData = [];

    protected $wheres = [];

    protected $whereData = [];

    protected $orWheres = [];

    protected $havingData = [];

    protected $having = [];

    protected $orHaving = [];

    /**
     * @var string
     */
    protected $groupBy;

    /**
     * @var string
     */
    protected $orderBy;

    protected $leftJoin = [];

    /**
     * @var string
     */
    protected $limit;

    /**
     * @var Query
     */
    protected $query;

    /**
     * 匹配普通变量正则
     *
     * @var string
     */
    protected $varPattern = '/^[\w0-9_]+$/i';

    /**
     * @var Where
     */
    protected $where;

    protected $options = [];

    public function __construct(Container $container, Query $query)
    {
        $this->container = $container;

        $this->query = $query;

        $this->where = new Where();
    }

    public function from(& $table)
    {
        $this->tableName = & $table;

        return $this;
    }

    public function where(& $p1, $p2 = '=', $p3 = null, $table = null)
    {
        $tb = $table ? $table : $this->tableName;
        if (! $tb) {
            throw new InvalidArgumentException('表名不能为空');
        }

//        $this->whereHandler($this->wheres, $tb, $p1, $p2, $p3, $this->whereData);
        $content = $this->where->table($tb)->build($p1, $p2, $p3)->pull();

        $this->wheres = array_merge($this->wheres, $content['where']);
//        $this->orWheres = array_merge($this->orWheres, $content['orWhere']);
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
     * 多对多关联(不支持AS别名)
     *
     * @param $mid string 中间表表名
     * @param $relate string 要关联的表
     *
     * 表结构 up      --- id
     *     user    --- id
     *     user_up --- user_id, up_id
    ->from('up')
    ->manyToMany('user_up', 'user')
    相当于:

    ->from('up')
    ->leftJoin('user_up', 'id', 'user_up.up_id')
    ->leftJoin('user', '`user`.id', 'user_up.user_id')

    SELECT * FROM `up`
    LEFT JOIN `user_up` ON `user_up`.up_id = `up`.`id`
    LEFT JOIN `user` ON `user_up`.user_id = `user`.id
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
     * 获取统计数量
     */
    public function count()
    {
        $t = 'COUNT(*) AS `TOTAL`';
        $r = $this->select($t)->readRow();
        return $r ? $r['TOTAL'] : 0;
    }

    public function sum($field, $as = 'SUM')
    {
        $t = "SUM(`$field`) AS `$as`";
        $this->select($t);
        return $this;
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

    public function orWhere(& $p1, $p2 = '=', $p3 = null, $table = null)
    {
        $tb = $table ? $table : $this->tableName;

        $content = $this->where->table($tb)->build($p1, $p2, $p3)->pull();

        $this->orWheres[] = '(' . implode(' AND ', $content['where']) . ')';
        $this->whereData = array_merge($this->whereData, $content['params']);

        return $this;
    }

    public function having(& $p1, $p2 = '=', $p3 = null, $table = null)
    {
        $tb = $table ? $table : $this->tableName;

        $content = $this->where->table($tb)->build($p1, $p2, $p3)->pull();

        $this->having = &$content['where'];
        $this->havingData = array_merge($this->havingData, $content['params']);
        return $this;
    }

    public function orHaving($p1, $p2 = '=', $p3 = null, $table = null)
    {
        $tb = $table ? $table : $this->tableName;

        $content = $this->where->table($tb)->build($p1, $p2, $p3)->pull();

        $this->orHaving = &$content['where'];
        $this->havingData = array_merge($this->havingData, $content['params']);
        return $this;
    }

    /**
     * INSERT IGNORE INTO `tb` SET ...
     *
     * @return static
     */
    public function ignore()
    {
        $this->options['ignore'] = 1;
        return $this;
    }

    /**
     *  传入：
     * [
    'id', 'parentId', 'name',
    'MenuContent' => ['content'], 'WechatMenuType' => ['code', 'menuType']
    ]
     * 返回:
    `table`.`id` AS id,`table`.`parent_id` AS parentId,`table`.`name` AS name,
    `menu_content`.`content` AS content,`wechat_menu_type`.`code` AS code,
    `wechat_menu_type`.`menu_type` AS menuTyp
     */
    public function select(& $data)
    {
        $this->fieldHandler($this->field, $data, $this->tableName);
        return $this;
    }

    protected function fieldHandler(& $fieldsContainer, & $data, $table)
    {
        if (! is_array($data)) {
            if (preg_match($this->varPattern, $data)) {
                $fieldsContainer .= '`' . $table . '`.`' . $data . '`, ';

            } else {
                $fieldsContainer .= $data . ', ';

            }

        } else {
            foreach ($data as $k => & $v) {
                if (is_numeric($k)) {
                    $this->fieldHandler($fieldsContainer, $v, $table);

                } else {
                    if (! is_array($v)) {
                        $fieldsContainer .= "`$table`.`$k` AS `$v`,";
                        continue;
                    }// $v是数组, $k是表名
                    $tb = $k;
                    foreach ($v as $i => & $f) {
                        if (is_numeric($i)) {
                            $this->fieldHandler($fieldsContainer, $f, $tb);

                        } else {
                            $fieldsContainer .= "`$tb`.`$i` AS `$f`,";

                        }
                    }
                }
            }
        }
    }

    public function querySql($clear = false)
    {
        $table  = "`$this->tableName`";

        $orWhere  = '';
        $having   = '';

        $fields = $this->getFieldsSql();
        $leftJoin = $this->getLeftJoinSql();
        $where = $this->getWhereSql();
        $orderBy = $this->getOrderBySql();
        $groupBy = $this->getGroupBySql();
        $limit = $this->getLimitSql();

        if ($groupBy) {
            $having = $this->getWhereSql(true);

            $this->whereData = array_merge($this->whereData, $this->havingData);
        }

        $params = $this->whereData;

        if ($clear) $this->clear();

        return [
            'sql' => "SELECT $fields FROM {$table}{$leftJoin}{$where}{$groupBy}{$orderBy}{$having}{$limit}",
            'params' => &$params
        ];
    }

    /**
     * 读取单行数据
     */
    public function readRow()
    {
        if (! $this->tableName) {
            throw new InternalServerError('Can not found table name.');
        }

        $content = $this->query->connection()->options($this->options)->one($this->querySql()['sql'] . ' LIMIT 1', $this->whereData);

        $this->clear();

        return $content ?: [];
    }

    /**
     * 读取多行数据
     */
    public function read()
    {
        if (! $this->tableName) {
            throw new InternalServerError('Can not found table name.');
        }

        $content = $this->query->connection()->options($this->options)->all($this->querySql()['sql'], $this->whereData);

        $this->clear();

        return $content ?: [];
    }

    /**
     * 获取绑定参数
     *
     * @return array
     */
    public function getBindParams()
    {
        return $this->whereData + $this->havingData;
    }

    public function sort($orderString)
    {
        $this->orderBy = " ORDER BY $orderString";

        return $this;
    }

    /**
     * 用法:
     *  $this->limit(0, 5); ===> LIMIT 0, 5
     *
     *  $this->limit(5);    ===> LIMIT 5
     */
    public function limit($p1, $p2 = 0)
    {
        if (! $p2) {
            $this->limit = " LIMIT $p1";
        } else
            $this->limit = " LIMIT $p1, $p2";
        return $this;
    }

    /**
     * 传入数组或字符串
     */
    public function group($data)
    {
        $this->groupHandler($this->groupBy, $this->tableName, $data);
        return $this;
    }

    protected function groupHandler(& $groupContainer, $table, & $data)
    {
        if (is_array($data)) {
            foreach ($data as $k => & $field) {
                // $field = $this->changeToUnderlineOne($field);
                if (is_numeric($k)) {
                    $groupContainer .= "`$table`.`$field`,";
                } else {
                    $groupContainer .= "`$k`.`$field`,";
                }
            }
            $groupContainer = ' GROUP BY ' . rtrim($groupContainer, ',');
        } else {
            if (preg_match($this->varPattern, $data)) {
                $groupContainer = " GROUP BY `$table`.`$data`";
            } else {
                $groupContainer = " GROUP BY $data";
            }
        }
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
        return $this->join($table, $p1, $p2, 'LEFT');
    }

    public function rightJoin(& $table, $p1 = null, $p2 = null, $condit = '=')
    {
        return $this->join($table, $p1, $p2, 'RIGHT');
    }

    public function join(& $table, $field1 = null, $field2 = null, $condit = '=', $prefix = '')
    {
        if (preg_match($this->varPattern, $table)) {
            $table = "`$table`";
        }

        $p1IsVar = preg_match($this->varPattern, $field1);
        $p2IsVar = preg_match($this->varPattern, $field2);

        if (! $p1IsVar && ! $p2IsVar) {
            $this->leftJoin[] = " $prefix JOIN $table ON $field1 $condit $field2";

        } elseif (! $p1IsVar) {
            $this->leftJoin[] = " $prefix JOIN $table ON $field1 $condit `{$this->tableName}`.`$field2`";

        } else {
            $this->leftJoin[] = " $prefix JOIN $table ON $field2 $condit `{$this->tableName}`.`$field1`";

        }
        return $this;
    }

    /**
     * @param $string
     * @return $this
     */
    public function joinRaw($string)
    {
        $this->leftJoin[] = &$string;
        return $this;
    }

    public function union()
    {

    }

    public function remove($id = null)
    {
        if ($id) {
            $k = 'id';
            $this->where($k, $id);
        }
        $where = $this->getWhereSql();
        $res = $this->query->connection()->delete($this->tableName, $where, $this->whereData);
        $this->clear();
        return $res;
    }

    public function delete($id = null)
    {
        return $this->remove($id);
    }

    public function insert(array & $p1)
    {
        $res = $this->query->connection()->options($this->options)->add($this->tableName, $p1);
        $this->clear();
        return $res;
    }

    public function replace(array & $p1)
    {
        $res = $this->query->connection()->options($this->options)->replace($this->tableName, $p1);
        $this->clear();
        return $res;
    }

    /**
     * 用法:
     * 	$this->update([
     * 		'name' => '张三', 'age' => 18
     * 	]);
     *
     *  $this->update('age', '+', 18);
     *
     *  $this->update('age', '+');
     *
     *  $this->update('age', '-');
     */
    public function update(& $p1, $p2 = null, $p3 = 1)
    {
        if ($p2) {
            switch ($p2) {
                case '+':
                    $p1 = ["`$p1` = `$p1` +" => $p3];
                    break;
                case '-':
                    $p1 = ["`$p1` = `$p1` -" => $p3];
                    break;
                default:
                    $p1 = [$p1 => $p2];
            }

        }

        $where = $this->getWhereSql();

        $res = $this->query->connection()->update($this->tableName, $p1, $where, $this->whereData);
        $this->clear();
        return $res;
    }

    // 字段值--
    public function incr($field, $step = 1)
    {
        return $this->update($field, '+', $step);
    }

    // 字段值++
    public function decr($field, $step = 1)
    {
        return $this->update($field, '-', $step);
    }

    // 批量新增
    public function batchInsert(&$data)
    {
        $res = $this->query->connection()->options($this->options)->batchAdd($this->tableName, $data);
        $this->clear();
        return $res;
    }

    protected function getOrderBySql()
    {
        return $this->orderBy;
    }

    protected function getLeftJoinSql()
    {
        if (count($this->leftJoin) > 0) {
            return implode(' ', $this->leftJoin);
        }
    }

    /**
     * 获取where字符串
     * */
    public function getWhereSql($isHaving = false, $isOrWhere = false)
    {
        $where  = '';

        $t = ' WHERE ';

        if ($isHaving) {
            $data   = & $this->having;
            $orData = & $this->orHaving;

            $t = ' HAVING ';
        } else {
            $data   = & $this->wheres;
            $orData = & $this->orWheres;
        }

        if (count($data) > 0) {
            $where .= implode(' AND ', $data);
        }

        if (count($orData) > 0) {
            if ($where) {
                $where .= ' OR ';
            }
            $where .= implode(' OR ', $orData);

        }

        if ($where) {
            $where = $t . $where;
        }

        return $where;

    }

    protected function getFieldsSql()
    {
        return $this->field ? rtrim($this->field, ', ') : '* ';
    }

    protected function getLimitSql()
    {
        return $this->limit;
    }

    public function clear()
    {
//        $this->tableName = null;
        $this->field     = null;
        $this->limit 	 = null;
        $this->orderBy	 = null;
        $this->groupBy	 = null;

        $this->whereData  = [];
        $this->havingData = [];
        $this->leftJoin   = [];
        $this->wheres     = [];
        $this->orWheres   = [];
        $this->having	  = [];
        $this->orHaving   = [];

        $this->options = [];
    }

    protected function getGroupBySql()
    {
        return $this->groupBy;
    }

}
