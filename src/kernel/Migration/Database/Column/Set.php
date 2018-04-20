<?php

namespace Lxh\Migration\Database\Column;

use Phinx\Db\Adapter\AdapterInterface;

class Set extends Column
{
    protected $type = AdapterInterface::PHINX_TYPE_SET;

    /**
     * @param array|string $values
     * @return $this
     */
    public function values($values)
    {
        return $this->setOption(
            'values', is_array($values) ? join(',', $values) : $values
        );
    }
}
