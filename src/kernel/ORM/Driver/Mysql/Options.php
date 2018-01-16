<?php

namespace Lxh\ORM\Driver\Mysql;

trait Options
{
    /**
     * @var array
     */
    protected $options = [];

    /**
     * INSERT IGNORE INTO `tb` SET ...
     *
     * @return $this
     */
    public function ignore()
    {
        $this->options['ignore'] = 1;
        return $this;
    }

    /**
     * @param array $options
     * @return $this
     */
    public function options(array $options)
    {
        $this->options = array_merge($this->options, $options);
        return $this;
    }
}
