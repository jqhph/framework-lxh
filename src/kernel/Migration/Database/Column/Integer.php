<?php

namespace Lxh\Migration\Database\Column;

use Phinx\Db\Adapter\AdapterInterface;
use Phinx\Db\Adapter\MysqlAdapter;

class Integer extends Column
{
    protected $type = AdapterInterface::PHINX_TYPE_INTEGER;

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

    public function big()
    {
        return $this->limit(MysqlAdapter::INT_BIG);
    }

    public function tiny()
    {
        return $this->limit(MysqlAdapter::INT_TINY);
    }

    public function regular()
    {
        return $this->limit(MysqlAdapter::INT_REGULAR);
    }

    public function medium()
    {
        return $this->limit(MysqlAdapter::INT_MEDIUM);
    }

    public function small()
    {
        return $this->limit(MysqlAdapter::INT_SMALL);
    }
}
