<?php


namespace Lxh\ORM\Driver\Mysql;

use Lxh\Exceptions\InternalServerError;
use Lxh\Contracts\Container\Container;
use Lxh\Exceptions\InvalidArgumentException;
use Lxh\ORM\Query;

class WhereBuilder
{
    /**
     * 匹配普通变量正则
     *
     * @var string
     */
    protected $varPattern = '/^[\w0-9_]+$/i';

    // 预处理绑定参数
    protected $params = [];

    /**
     * @var string
     */
    protected $table = null;

    /**
     * 设置表名
     *
     * @param $tb
     * @return $this
     */
    public function table($tb)
    {
        $this->table = "`{$tb}`.";
        return $this;
    }

    public function build(& $p1, $p2 = null, $p3 = null, &$autoAddTable = true)
    {
        $wheres = $this->handle($p1, $p2, $p3, $autoAddTable);
        $params = $this->params;
        $this->params = [];

        return [
            'where' => &$wheres,
            'params' => &$params,
        ];
    }

    /**
     * 解析where条件字句
     *
     * @param string|array $p1 where字句字段
     * @param string|array|null $p2 操作符或条件值
     * @param string|array $p3条件值
     * @param bool $autoAddTable 是否给字段加表名
     */
    protected function handle(& $p1, $p2 = null, $p3 = null, &$autoAddTable = true)
    {
        $data = [];

        if (is_array($p1)) {
            $data = array_merge($data, $this->prepareArrayParams($p1, $autoAddTable));

        } elseif($p3 === null) {
            if ($p1 == 'or' || $p1 == 'OR') {
                $ors = $this->handle($p2, null, null, $autoAddTable);
                $data[] = '(' . implode(' OR ', $ors)  . ')';

            } else {
                $this->normalizeWhereField($autoAddTable, $p1);
                switch (strtolower($p2)) {
                    case 'is not null':
                        $data[] = "$p1 IS NOT NULL";
                        break;
                    case 'is null':
                        $data[] = "$p1 IS NULL";
                        break;
                    default:
                        // where字句
                        $data[] 	   = "$p1 = ?";
                        // 预处理绑定参数
                        $this->params[] = $p2;
                        break;
                }
            }

        } else {
            $data = array_merge($data, $this->prepareStringParams($p1, $p2, $p3, $autoAddTable));
        }

        return $data;
    }

    protected function prepareStringParams(&$p1, &$p2, &$p3, &$autoAddTable)
    {
        $this->normalizeWhereField($autoAddTable, $p1);

        $data = [];
        switch (strtolower($p2)) {
            case '%like':
            case '%*':
                $data[] 	   = "$p1 LIKE ?";
                $this->params[] = "%{$p3}";
                break;
            case 'like%':
            case '*%':
                $data[] 	   = "$p1 LIKE ?";
                $this->params[] = "{$p3}%";
                break;
            case '%like%':
            case '%*%':
                $data[] 	   = "$p1 LIKE ?";
                $this->params[] = "%{$p3}%";
                break;
            case 'between':
                $data[] = "($p1 BETWEEN ? AND ?)";
                $this->params[] = $p3[0];
                $this->params[] = $p3[1];
                break;
            case 'in':
                if (count($p3) > 1) {
                    foreach ($p3 as &$v) {
                        $this->params[] = $v;

                        $v = '?';
                    }
                    $data[] = $p1 . ' IN (' . implode(',', $p3) . ')';
                } else {
                    $this->params[] = $p3[0];
                    $data[] = $p1 . ' = ?';
                }
                break;
            case 'not in':
                foreach ($p3 as & $v) {
                    $this->params[] = $v;

                    $v = '?';
                }
                $data[] = $p1 . ' NOT IN (' . implode(',', $p3) . ')';
                break;
            default:
                $data[] 	   = "$p1 $p2 ?";
                $this->params[] = $p3;
                break;
        }

        return $data;
    }

    protected function prepareArrayParams(&$params, &$autoAddTable)
    {
        $data = [];

        foreach ($params as $field => & $val) {
            if (! is_array($val)) {
                $data[] = $this->handle($field, $val)[0];
                continue;
            }

            if ($field == 'or' || $field == 'OR') {
                $ors = $this->handle($val, null, null, $autoAddTable);
                $data[] = '(' . implode(' OR ', $ors)  . ')';
            } elseif ($field == 'or+' || $field == 'OR+') {
                //$where = [
                //    'OR+' => [
                //        ['f1' => $f1, 'f2' => $f2],
                //        ['f3' => $f3, f4 => $f4]
                //    ]
                //];
                // ((`tb`.`f1` = ? AND `tb`.`f2` = ?) OR (`tb`.`f3` = ? AND `tb`.`f4` = ?))
                $and = [];
                foreach ($val as &$p) {
                    $and[] = '(' . implode(' AND ', $this->handle($p, null, null, $autoAddTable)) . ')';
                }

                $data[] = '(' . implode(' OR ', $and)  . ')';

            } else {
                if (count($val) < 2) {
                    throw new InvalidArgumentException('where字句构造参数错误！');
                }
                $data[] = $this->handle($field, $val[0], $val[1], $autoAddTable)[0];
            }
        }

        return $data;
    }

    protected function normalizeWhereField(& $autoAddTable, & $field)
    {
        if (preg_match($this->varPattern, $field)) {
            $field = $autoAddTable ? "{$this->table}`$field`" : "`$field`";
        }
    }
}
