<?php

namespace Lxh\Migration\Database\Column;

use Phinx\Db\Adapter\AdapterInterface;

class Integer extends Column
{
    protected $type = AdapterInterface::PHINX_TYPE_INTEGER;
}
