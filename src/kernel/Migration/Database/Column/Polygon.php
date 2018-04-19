<?php

namespace Lxh\Migration\Database\Column;

use Phinx\Db\Adapter\AdapterInterface;

class Polygon extends Column
{
    protected $type = AdapterInterface::PHINX_TYPE_POLYGON;
}
