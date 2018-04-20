<?php

namespace Lxh\Migration\Database\Column;

use Phinx\Db\Adapter\AdapterInterface;
use Phinx\Db\Adapter\MysqlAdapter;

class Text extends Column
{
    protected $type = AdapterInterface::PHINX_TYPE_TEXT;

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

    public function long()
    {
        return $this->limit(MysqlAdapter::TEXT_LONG);
    }

    public function tiny()
    {
        return $this->limit(MysqlAdapter::TEXT_TINY);
    }

    public function regular()
    {
        return $this->limit(MysqlAdapter::TEXT_REGULAR);
    }

    public function medium()
    {
        return $this->limit(MysqlAdapter::TEXT_MEDIUM);
    }

    public function small()
    {
        return $this->limit(MysqlAdapter::TEXT_SMALL);
    }
}
