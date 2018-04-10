<?php

namespace Lxh\ORM\Driver\Mysql;

use Lxh\Exceptions\InternalServerError;
use Lxh\Contracts\Container\Container;
use Lxh\Exceptions\InvalidArgumentException;
use Lxh\ORM\Query;

class Builder
{
    use Field, Join, GroupBy, Where, Having, Options;

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
    protected $orderBy;

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
     * @var WhereBuilder
     */
    protected $whereBuilder;

    public function __construct(Container $container, Query $query)
    {
        $this->container = $container;
        $this->query = $query;
        $this->whereBuilder = new WhereBuilder();
    }

    public function from(& $table)
    {
        $this->tableName = & $table;

        return $this;
    }

    /**
     * @param bool $clear
     * @return array
     */
    public function querySql($clear = false)
    {
        $table  = "`$this->tableName`";

        $orWhere  = '';
        $having   = '';

        $fields = $this->getFieldsString();
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
     *
     * @return array
     * @throws InternalServerError
     */
    public function findOne()
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
     *
     * @return array
     * @throws InternalServerError
     */
    public function find()
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

    public function union()
    {

    }

    /**
     * @param null $id
     * @return mixed
     * @throws InvalidArgumentException
     */
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

    /**
     * @param null $id
     * @return mixed
     */
    public function delete($id = null)
    {
        return $this->remove($id);
    }

    /**
     * @param array $p1
     * @return bool
     */
    public function insert(array & $p1)
    {
        $res = $this->query->connection()->options($this->options)->add($this->tableName, $p1);
        $this->clear();
        return $res;
    }

    /**
     * @param array $p1
     * @return mixed
     */
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
     *  $this->update('age', '+');
     *  $this->update('age', '-');
     *
     * @param $p1
     * @param null $p2
     * @param int $p3
     * @return int
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

    /**
     * 字段值--
     *
     * @param $field
     * @param int $step
     * @return int
     */
    public function incr($field, $step = 1)
    {
        return $this->update($field, '+', $step);
    }

    /**
     * 字段值++
     *
     * @param $field
     * @param int $step
     * @return int
     */
    public function decr($field, $step = 1)
    {
        return $this->update($field, '-', $step);
    }

    /**
     * 批量新增
     *
     * @param $data
     * @return mixed
     */
    public function batchInsert(array &$data)
    {
        $res = $this->query->connection()->options($this->options)->batchAdd($this->tableName, $data);
        $this->clear();
        return $res;
    }

    /**
     * 批量replace
     *
     * @param $data
     * @return mixed
     */
    public function batchReplace(array &$data)
    {
        $res = $this->query->connection()->options($this->options)->batchAdd($this->tableName, $data, true);
        $this->clear();
        return $res;
    }

    /**
     * @return string
     */
    protected function getOrderBySql()
    {
        return $this->orderBy;
    }

    /**
     * 获取where字符串
     *
     * @param bool $isHaving
     * @param bool $isOrWhere
     * @return string
     */
    public function getWhereSql($isHaving = false)
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

    /**
     * @return string
     */
    protected function getLimitSql()
    {
        return $this->limit;
    }

    /**
     * @return void
     */
    public function clear()
    {
//        $this->tableName = null;
        $this->field     = null;
        $this->limit 	 = null;
        $this->orderBy	 = null;
        $this->groupBy	 = null;

        $this->whereData  = [];
        $this->havingData = [];
        $this->join   = [];
        $this->wheres     = [];
        $this->orWheres   = [];
        $this->having	  = [];
        $this->orHaving   = [];

        $this->options = [];
    }

}
