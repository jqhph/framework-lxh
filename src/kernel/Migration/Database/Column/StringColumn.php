<?php

namespace Lxh\Migration\Database\Column;

use Phinx\Db\Adapter\AdapterInterface;

class StringColumn extends Column
{
    protected $type = AdapterInterface::PHINX_TYPE_STRING;

    /**
     * 设置字段的 collation （仅适用于 MySQL）
     *
     * @param $c
     * @return $this
     */
    public function collation($c)
    {
        return $this->setOption('collation', $c);
    }

    /**
     * 设置字段的 encoding （仅适用于 MySQL）
     *
     * @param $e
     * @return $this
     */
    public function encoding($e)
    {
        return $this->setOption('encoding', $e);
    }
}
