<?php

namespace Lxh\Migration\Database\Column;

use Phinx\Db\Adapter\AdapterInterface;

class Boolean extends Column
{
    protected $type = AdapterInterface::PHINX_TYPE_BOOLEAN;

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
