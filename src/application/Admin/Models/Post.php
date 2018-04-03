<?php

namespace Lxh\Admin\Models;

use Lxh\Auth\AuthManager;
use Lxh\Auth\Database\Models;
use Lxh\Contracts\Container\Container;
use Lxh\Helper\Entity;
use Lxh\MVC\Model;
use Lxh\MVC\Session;
use Lxh\Support\Password;

class Post extends \Lxh\Auth\Database\Admin
{
    protected function afterDelete($id, $result, $trash)
    {
        if ($result) {
            if ($trash) {
                $table = $this->trashTableName;
            } else {
                $table = $this->tableName;
            }
            $actionAdmin = operations_logger()->adminAction($this);

            $actionAdmin->table = $table;

            $actionAdmin->setDelete()->add();
        }
    }

    protected function afterAdd($insertId, array &$input)
    {
        if ($insertId) {
            $this->setId($insertId);

            operations_logger()->adminAction($this)->setInsert()->add();
        }
    }

    protected function afterUpdate($id, array &$input, $result)
    {
        if ($result) {
            operations_logger()->adminAction($this)->setUpdate()->add();
        }
    }

    protected function afterToTrash($id, $result)
    {
        if ($result) {
            operations_logger()->adminAction($this)->setMoveToTrash()->add();
        }
    }

    protected function afterBatchToTrash(array $ids, $res)
    {
        if ($res) {
            $action = operations_logger()->adminAction($this);

            $action->input = implode(',', $ids);
            $action->setBatchMoveToTrash()->add();
        }
    }

    protected function afterRestore(array $ids, $res)
    {
        if ($res) {
            $action = operations_logger()->adminAction($this);

            $action->input = implode(',', $ids);
            $action->setRestore()->add();
        }
    }
}
