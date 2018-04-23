<?php

namespace Lxh\Migration\Database;

class Index
{
    /**
     * @var TableHelper
     */
    protected $table;

    /**
     * @var mixed
     */
    protected $columns;

    /**
     * @var array
     */
    protected $options = [];

    public function __construct(TableHelper $table, $columns)
    {
        $this->table   = $table;
        $this->columns = $columns;
    }

    /**
     * Mysql支持
     *
     * @return Index
     */
    public function fulltext()
    {
        return $this->setOption('type', 'fulltext');
    }

    /**
     * 设置唯一索引
     *
     * @return Index
     */
    public function unique()
    {
        return $this->setOption('unique', true);
    }

    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * 索引名称
     *
     * @param $name
     * @return Index
     */
    public function name($name)
    {
        return $this->setOption('name', $name);
    }

    public function setOption($k, $v)
    {
        $this->options[$k] = &$v;
        return $this;
    }

    public function getOptions()
    {
        return $this->options;
    }


}
