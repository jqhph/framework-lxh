<?php

namespace Lxh\Migration\Database\Column;

use Phinx\Db\Adapter\AdapterInterface;
use Phinx\Db\Adapter\MysqlAdapter;

class Blob extends Column
{
    protected $type = AdapterInterface::PHINX_TYPE_BLOB;

    public function long()
    {
        return $this->limit(MysqlAdapter::BLOB_LONG);
    }

    public function tiny()
    {
        return $this->limit(MysqlAdapter::BLOB_TINY);
    }

    public function regular()
    {
        return $this->limit(MysqlAdapter::BLOB_REGULAR);
    }

    public function medium()
    {
        return $this->limit(MysqlAdapter::BLOB_MEDIUM);
    }

    public function small()
    {
        return $this->limit(MysqlAdapter::BLOB_SMALL);
    }
}
