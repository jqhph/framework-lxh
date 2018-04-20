<?php

namespace Lxh\Migration\Database\Column;

use Phinx\Db\Adapter\AdapterInterface;

class Timestamp extends Column
{
    protected $type = AdapterInterface::PHINX_TYPE_TIMESTAMP;

    /**
     *
     * @return Column
     */
    public function current()
    {
        return $this->default('CURRENT_TIMESTAMP');
    }

    /**
     * 开启或关闭 with time zone 选项
     *
     * @param bool $flag
     * @return $this
     */
    public function timezone($flag = true)
    {
        return $this->setOption('timezone', $flag);
    }
}
