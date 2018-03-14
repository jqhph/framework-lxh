<?php
namespace Lxh\MVC;

use Lxh\Exceptions\Exception;
use Lxh\Exceptions\InternalServerError;
use Lxh\Contracts\Container\Container;
use Lxh\Exceptions\InvalidArgumentException;
use Lxh\Helper\Entity;
use Lxh\Helper\Util;
use Lxh\ORM\Query;
use Lxh\Contracts\Events\Dispatcher;

class Model extends Entity
{
    /**
     * id字段名称
     *
     * @var string
     */
    protected $primaryKeyName = 'id';

    protected $deleteKeyName = 'deleted';

    /**
     * 默认查询的字段
     *
     * @var string|array
     */
    protected $selectFields = '*';

    /**
     * @var string
     */
    protected $module = '';

    /**
     * 模型名称
     *
     * @var string
     */
    protected $name;

    /**
     * 表名
     *
     * @var string
     */
    protected $tableName;

    /**
     * 新插入的id
     *
     * @var mixed
     */
    protected $insertId;

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var string
     */
    protected $connectionType = 'primary';

    /**
     * @var Dispatcher
     */
    protected $events;

    protected $queries = [];

    public function __construct($name = null, Container $container = null)
    {
        $name = $name ?: $this->parseName();

        $this->name = lc_dash($name);
        $this->module = defined('__MODULE_DASH__') ? __MODULE_DASH__ : '';
        $this->container = $container ?: container();
        $this->events = $container['events'];

        if (! $this->tableName)
            $this->tableName = __camel_case__($name);

        $this->initialize();
    }

    /**
     * 获取当前模型类名
     *
     * @return mixed
     */
    protected function parseName()
    {
        $names = explode('\\', static::class);
        return end($names);
    }

    /**
     * 模型类型
     *
     * @return mixed
     */
    public function getMorphType()
    {
        throw new Exception('没有定义类型');
    }

    /**
     * 设置id名称
     *
     * @param $name
     * @return $this
     */
    public function setIdName($name)
    {
        $this->primaryKeyName = $name;
        return $this;
    }

    public function setDeleteKeyName($name)
    {
        $this->deleteKeyName = $name;
        return $this;
    }

    public function getDeleteKeyName()
    {
        return $this->deleteKeyName;
    }

    /**
     * 设置id
     *
     * @param $id
     * @return $this
     */
    public function setId($id)
    {
        $this->set($this->primaryKeyName, $id);

        return $this;
    }

    /**
     * 初始化操作
     *
     * @return void
     */
    protected function initialize()
    {
    }

    /**
     * 获取记录总数
     *
     * @param array $where
     * @return int
     */
    public function count(array $where)
    {
        $q = $this->query();

        if ($where) $q->where($where);

        return $q->count();
    }

    /**
     * 批量删除方法
     *
     * @param array $ids
     */
    public function batchDelete(array $ids)
    {
        if (! $ids) return false;

        $this->beforeBatchDelete($ids);
        fire(
            "{$this->module}.{$this->name}.batch-delete.before",
            [$ids]
        );

        if (count($ids) > 1) {
            $res = $this->query()->where($this->primaryKeyName, 'IN', $ids)->delete();
        } else {
            $res = $this->query()->where($this->primaryKeyName, $ids[0])->delete();
        }

        $this->afterBatchDelete($ids, $res);
        fire(
            "{$this->module}.{$this->name}.batch-delete.after",
            [$ids]
        );

        return $res;
    }

    /**
     * 批量还原方法
     *
     * @param array $ids
     */
    public function restore(array $ids)
    {
        if (! $ids) return false;

        $this->beforeRestore($ids);
        fire(
            "{$this->module}.{$this->name}.restore.before",
            [$ids]
        );

        if (count($ids) > 1) {
            $res = $this->query()
                ->where($this->primaryKeyName, 'IN', $ids)
                ->update([$this->deleteKeyName => 0]);
        } else {
            $res = $this->query()
                ->where($this->primaryKeyName, $ids[0])
                ->update([$this->deleteKeyName => 0]);
        }

        $this->afterRestore($ids, $res);
        fire(
            "{$this->module}.{$this->name}.restore.after",
            [$ids]
        );

        return $res;
    }

    protected function beforeRestore($ids)
    {
    }

    protected function afterRestore($ids, $res)
    {
    }

    /**
     * 批量删除方法
     *
     * @param array $ids
     */
    public function batchToTrash(array $ids)
    {
        if (! $ids) return false;

        $this->beforeBatchToTrash($ids);
        fire(
            "{$this->module}.{$this->name}.batch-to-trash.before",
            [$ids]
        );

        if (count($ids) > 1) {
            $res = $this->query()
                ->where($this->primaryKeyName, 'IN', $ids)
                ->update([$this->deleteKeyName => 1]);
        } else {
            $res = $this->query()
                ->where($this->primaryKeyName, $ids[0])
                ->update([$this->deleteKeyName => 1]);
        }

        $this->afterBatchToTrash($ids, $res);
        fire(
            "{$this->module}.{$this->name}.batch-to-trash.after",
            [$ids]
        );

        return $res;
    }

    protected function beforeBatchToTrash($ids)
    {
    }

    protected function afterBatchToTrash($ids, $res)
    {
    }


    /**
     * @param array $ids
     */
    protected function beforeBatchDelete(array &$ids)
    {
    }

    /**
     * @param array $ids
     * @param $effect
     */
    protected function afterBatchDelete(array &$ids, $effect)
    {
    }

    /**
     * 获取id
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->get($this->primaryKeyName);
    }

    /**
     * @return string
     */
    public function getKeyName()
    {
        return $this->primaryKeyName;
    }

    /**
     * 查找记录列表
     *
     * @param array $where
     * @param string $order
     * @param int $offset
     * @param int $limit
     */
    public function findList(array $where, $order = 'id DESC', $offset = 0, $limit = 20)
    {
        $q = $this->query()->select($this->selectFields)->where($where);

        if ($order) $q->sort($order);

        if ($limit) {
            $q->limit($offset, $limit);
        }
        return $q->find();
    }

    /**
     * where
     *
     * @return Query
     */
    public function where()
    {
        return call_user_func_array([$this->query()->select($this->selectFields), 'where'], func_get_args());
    }

    /**
     * whereIn
     *
     * @return Query
     */
    public function whereIn()
    {
        return call_user_func_array([$this->query()->select($this->selectFields), 'whereIn'], func_get_args());
    }

    /**
     * @param array $ids
     * @return Query
     */
    public function whereInIds(array $ids)
    {
        return $this->query()->select($this->selectFields)->whereIn($this->getKeyName(), $ids);
    }

    /**
     * where
     *
     * @return Query
     */
    public function whereOr()
    {
        return call_user_func_array([$this->query()->select($this->selectFields), 'whereOr'], func_get_args());
    }

    /**
     * where
     *
     * @return Query
     */
    public function whereOrs()
    {
        return call_user_func_array([$this->query()->select($this->selectFields), 'whereOrs'], func_get_args());
    }

    /**
     * select
     *
     * @return Query
     */
    public function select($fields = null)
    {
        return $this->query()->select($fields ?: $this->selectFields);
    }

    // 查找数据
    public function find()
    {
        $id = $this->getId();

        if ($id) {
            $data = $this->query()->select($this->selectFields)->where($this->primaryKeyName, $id)->findOne();
            $this->fill($data);
            return $data;
        }
        return $this->query()->select($this->selectFields)->find();
    }

    /**
     * 保存操作，如果没有设置id，则新增
     *
     * @return bool
     */
    public function save()
    {
        $input = $this->all();

        if (empty($input[$this->primaryKeyName])) {
            return $this->add();
        }
        $id = $input[$this->primaryKeyName];

        unset($input[$this->primaryKeyName]);

        $this->beforeUpdate($id, $input);
        fire(
            "{$this->module}.{$this->name}.update.before",
            [$id, &$input]
        );

        $result = $this->query()->where($this->primaryKeyName, $id)->update($input);

        $this->afterUpdate($id, $input, $result);
        fire(
            "{$this->module}.{$this->name}.update.after",
            [$id, &$input, $result]
        );

        return $result;
    }

    /**
     * @return bool
     */
    public function add()
    {
        $input = $this->all();
        if (! $input) {
            throw new InvalidArgumentException('新增数据错误，参数不能为空');
        }

        $this->beforeAdd($input);
        fire(
            "{$this->module}.{$this->name}.add.before",
            [&$input]
        );

        $this->insertId = $this->query()->add($input);
        if ($this->insertId) {
            $this->setId($this->insertId);
        }

        $this->afterAdd($this->insertId, $input);
        fire(
            "{$this->module}.{$this->name}.add.after",
            [$this->insertId, &$input]
        );

        return $this->insertId;
    }

    /**
     * replace
     *
     * @return bool
     */
    public function replace()
    {
        $input = $this->all();

        $this->beforeAdd($input);
        fire(
            "{$this->module}.{$this->name}.add.before",
            [&$input]
        );

        $this->insertId = $this->query()->replace($input);

        $this->afterAdd($this->insertId, $input);
        fire(
            "{$this->module}.{$this->name}.add.after",
            [$this->insertId, &$input]
        );

        return $this->insertId;
    }

    /**
     * 把数据移植回收站
     *
     * @return bool
     */
    public function toTrash()
    {
        $id = $this->getId();
        if (empty($id)) {
            return false;
        }

        $this->beforeToTrash($id);
        fire(
            "{$this->module}.{$this->name}.to-trash.before",
            [&$id]
        );

        $result = $this->query()
            ->where($this->primaryKeyName, $id)
            ->update([$this->deleteKeyName => 1]);

        $this->afterToTrash($id, $result);
        fire(
            "{$this->module}.{$this->name}.to-trash.after",
            [&$id, $result]
        );

        return $result;
    }

    protected function beforeToTrash($id)
    {
    }

    protected function afterToTrash($id, $result)
    {
    }

    /**
     * 删除一条记录
     *
     * @return bool|mixed
     */
    public function delete()
    {
        $id = $this->getId();
        if (empty($id)) {
            return false;
        }

        $this->beforeDelete($id);
        fire(
            "{$this->module}.{$this->name}.delete.before",
            [&$id]
        );

        $result = $this->query()->where($this->primaryKeyName, $id)->delete();

        $this->afterDelete($id, $result);
        fire(
            "{$this->module}.{$this->name}.delete.after",
            [&$id, $result]
        );

        return $result;
    }

    /**
     * 删除操作前置钩子方法
     *
     * @param  string $id
     * @return mixed
     */
    protected function beforeDelete($id)
    {
    }

    /**
     * 删除操作后置钩子方法
     *
     * @param  string $id
     * @param  bool   $result 删除结果
     * @return mixed
     */
    protected function afterDelete($id, $result)
    {
    }

    /**
     * 获取新增记录的id
     *
     * @return mixed
     */
    public function getInsertId()
    {
        return $this->insertId;
    }

    /**
     * 获取表名
     *
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * 新增操作钩子方法，新增前调用
     *
     * @param array $input
     */
    protected function beforeAdd(array &$input)
    {
    }

    /**
     * 新增操作钩子方法
     *
     * @param $insertId
     * @param array $input
     */
    protected function afterAdd($insertId, array &$input)
    {
    }

    /**
     * 修改钩子方法，修改前调用
     *
     * @param $id
     * @param array $input
     */
    protected function beforeUpdate($id, array &$input)
    {
    }

    /**
     * 修改钩子方法
     *
     * @param $id
     * @param array $input
     * @param $result
     */
    protected function afterUpdate($id, array &$input, $result)
    {
    }

    /**
     * @return Query
     */
    public function query($name = null)
    {
        return query($name ?: $this->connectionType)->from($this->tableName);
    }

}
