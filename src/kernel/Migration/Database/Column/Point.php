<?php

namespace Lxh\Migration\Database\Column;

use Phinx\Db\Adapter\AdapterInterface;

class Point extends Column
{
    protected $type = AdapterInterface::PHINX_TYPE_POINT;
}
