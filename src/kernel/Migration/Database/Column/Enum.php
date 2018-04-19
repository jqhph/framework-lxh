<?php

namespace Lxh\Migration\Database\Column;

use Phinx\Db\Adapter\AdapterInterface;

class Enum extends Column
{
    protected $type = AdapterInterface::PHINX_TYPE_ENUM;
}
