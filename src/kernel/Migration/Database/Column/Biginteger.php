<?php

namespace Lxh\Migration\Database\Column;

use Phinx\Db\Adapter\AdapterInterface;

class Biginteger extends Column
{
    protected $type = AdapterInterface::PHINX_TYPE_BIG_INTEGER;
}
