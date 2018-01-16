<?php

namespace Lxh\ORM\Driver\Mysql;

trait Field
{
    /**
     * @var string
     */
    protected $field;

    /**
     *  传入：
         * [
            'id', 'parentId', 'name',
            'MenuContent' => ['content'], 'WechatMenuType' => ['code', 'menuType']
        ]
     * 返回:
        `table`.`id` AS id,`table`.`parent_id` AS parentId,`table`.`name` AS name,
        `menu_content`.`content` AS content,`wechat_menu_type`.`code` AS code,
        `wechat_menu_type`.`menu_type` AS menuTyp
     */
    public function select(& $data)
    {
        $this->field = &$data;
        return $this;
    }

    /**
     * @return int
     */
    public function count()
    {
        $t = 'COUNT(*) AS `TOTAL`';
        $r = $this->select($t)->readRow();
        return $r ? $r['TOTAL'] : 0;
    }

    /**
     * @param $field
     * @param string $as
     * @return $this
     */
    public function sum($field, $as = 'SUM')
    {
        $t = "SUM(`$field`) AS `$as`";
        $this->select($t);
        return $this;
    }


    /**
     * @param $fieldsContainer
     * @param $data
     * @param $table
     */
    protected function formatFieldsString(& $fieldsContainer, &$data, $table)
    {
        if (! is_array($data)) {
            if (preg_match($this->varPattern, $data)) {
                $fieldsContainer .= '`' . $table . '`.`' . $data . '`, ';
            } else {
                $fieldsContainer .= $data . ', ';
            }
            return;
        }
        foreach ($data as $k => & $v) {
            if (is_numeric($k)) {
                $this->formatFieldsString($fieldsContainer, $v, $table);

            } else {
                if (! is_array($v)) {
                    $fieldsContainer .= "`$table`.`$k` AS `$v`,";
                    continue;
                }// $v是数组, $k是表名
                $tb = $k;
                foreach ($v as $i => & $f) {
                    if (is_numeric($i)) {
                        $this->formatFieldsString($fieldsContainer, $f, $tb);
                    } else {
                        $fieldsContainer .= "`$tb`.`$i` AS `$f`,";
                    }
                }
            }
        }

    }

    protected function getFieldsString()
    {
        $fields = '';
        $this->formatFieldsString($fields, $this->field, $this->tableName);

        return rtrim($fields, ', ') ?: '* ';
    }

}