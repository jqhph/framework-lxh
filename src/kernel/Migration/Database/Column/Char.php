<?php

namespace Lxh\Migration\Database\Column;

use Phinx\Db\Adapter\AdapterInterface;

class Char extends Column
{
    protected $type = AdapterInterface::PHINX_TYPE_CHAR;

    public function __construct($name)
    {
        parent::__construct($name);

        $this->default('');
    }
}
