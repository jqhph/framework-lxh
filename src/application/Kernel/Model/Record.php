<?php
/**
 * 公共模型
 *
 * @author Jqh
 * @date   2017/7/28 16:09
 */

namespace Lxh\Kernel\Model;

use Lxh\MVC\Model;

class Record extends Model
{
    protected $selectFields = [];

    /**
     * 获取记录总条数
     *
     * @param  array | string $where
     * @return int
     */
    public function count($where)
    {
        $q = $this->query();

        if ($where) {
            $q->where($where);
        }

        return $q->count();
    }

    /**
     * 获取列表页数据
     *
     * @param  array | string $where
     * @param  int $offset
     * @param  int $maxSize
     * @param  string $orderString
     * @return array
     */
    public function records($where, $page, $maxSize, $orderString = 'id Desc')
    {
        $q = $this->query()
            ->select($this->selectFields)
            ->limit(($page - 1) * $maxSize, $maxSize);

        if ($where) {
            $q->where($where);
        }

        if ($orderString) {
            $q->sort($orderString);
        }

        return $q->find();
    }
}
