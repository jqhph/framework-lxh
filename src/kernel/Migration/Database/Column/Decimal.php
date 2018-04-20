<?php

namespace Lxh\Migration\Database\Column;

use Phinx\Db\Adapter\AdapterInterface;

class Decimal extends Column
{
    protected $type = AdapterInterface::PHINX_TYPE_DECIMAL;

    /**
     * 和 scale 组合设置精度
     *
     * @param $value
     * @return $this
     */
    public function precision($value)
    {
        return $this->setOption('precision', $value);
    }

    /**
     * 和 precision 组合设置精度
     *
     * @param $value
     * @return $this
     */
    public function scale($value)
    {
        return $this->setOption('scale', $value);
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
