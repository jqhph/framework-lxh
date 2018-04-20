<?php

namespace Lxh\Migration\Database\Column;

use Phinx\Db\Adapter\AdapterInterface;

class Biginteger extends Column
{
    protected $type = AdapterInterface::PHINX_TYPE_BIG_INTEGER;

    /**
     * 开启或关闭自增长
     *
     * @param bool $flag
     * @return $this
     */
    public function identity($flag = true)
    {
        return $this->setOption('identity', $flag);
    }

    /**
     * 开启或关闭 unsigned 选项（仅适用于 MySQL）
     *
     * @param bool $bool
     * @return $this
     */
    public function signed($bool = false)
    {
        return $this->setOption('signed', $bool);
    }
}
