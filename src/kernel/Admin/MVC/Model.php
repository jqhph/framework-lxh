<?php

namespace Lxh\Admin\MVC;

use Lxh\MVC\Model as Base;

class Model extends Base
{
    /**
     * 批量删除方法
     *
     * @param array $ids
     */
    public function batchDelete(array $ids)
    {
        if ($this->beforeDelete($ids) === false) {
            return false;
        }

        if (count($ids) > 1) {
            $res = $this->query()->where(static::$idFieldsName, 'IN', $ids)->delete();
        } else {
            $res = $this->query()->where(static::$idFieldsName, $ids[0])->delete();
        }

        $this->afterBatchDelete($ids);

        return $res;
    }

    protected function beforeBatchDelete(array &$ids)
    {
    }

    protected function afterBatchDelete(array &$ids)
    {
    }
}
