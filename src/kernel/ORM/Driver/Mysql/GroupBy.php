<?php

namespace Lxh\ORM\Driver\Mysql;

trait GroupBy
{
    /**
     * @var string
     */
    protected $groupBy;

    /**
     *
     * @param string|array $data
     * @return $this
     */
    public function group($data)
    {
        $this->formatGroupString($this->groupBy, $this->tableName, $data);
        return $this;
    }

    protected function getGroupBySql()
    {
        return $this->groupBy;
    }

    protected function formatGroupString(& $groupContainer, $table, & $data)
    {
        if (is_array($data)) {
            foreach ($data as $k => & $field) {
                // $field = $this->changeToUnderlineOne($field);
                if (is_numeric($k)) {
                    $groupContainer .= "`$table`.`$field`,";
                } else {
                    $groupContainer .= "`$k`.`$field`,";
                }
            }
            $groupContainer = ' GROUP BY ' . rtrim($groupContainer, ',');
        } else {
            if (preg_match($this->varPattern, $data)) {
                $groupContainer = " GROUP BY `$table`.`$data`";
            } else {
                $groupContainer = " GROUP BY $data";
            }
        }
    }
}
