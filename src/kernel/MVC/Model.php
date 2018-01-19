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
    protected static $idFieldsName = 'id';

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
    protected $modelName;

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
        $this->modelName = lc_dash($name ?: $this->parseName());
        $this->module = __MODULE_DASH__;

        if (! $this->tableName) $this->tableName = Util::convertWith($name, true);

        $this->container = $container ?: container();

        $this->events = $container['events'];

        $this->initialize();
    }

    protected function parseName()
    {
        $names = explode('\\', __CLASS__);

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
        static::$idFieldsName = $name;
        return $this;
    }

    /**
     * 设置id
     *
     * @param $id
     * @return $this
     */
    public function setId($id)
    {
        $this->set(static::$idFieldsName, $id);

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
        if ($this->beforeDelete($ids) === false) {
            return false;
        }

        if (count($ids) > 1) {
            $res = $this->query()->where(static::$idFieldsName, 'IN', $ids)->delete();
        } else {
            $res = $this->query()->where(static::$idFieldsName, $ids[0])->delete();
        }

        $this->afterBatchDelete($ids, $res);

        return $res;
    }

    protected function beforeBatchDelete(array &$ids)
    {
        fire(
            "{$this->module}.{$this->modelName}.batch-delete.before",
            [$ids]
        );
    }

    protected function afterBatchDelete(array &$ids, $effect)
    {
        fire(
            "{$this->module}.{$this->modelName}.batch-delete.after",
            [$ids]
        );
    }

    /**
     * 获取id
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->get(static::$idFieldsName);
    }

    /**
     * @return string
     */
    public function getKeyName()
    {
        return static::$idFieldsName;
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
            $data = $this->query()->select($this->selectFields)->where(static::$idFieldsName, $id)->findOne();
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
        $data = $this->all();

        if (empty($data[static::$idFieldsName])) {
            return $this->add();
        }
        $id = $data[static::$idFieldsName];

        unset($data[static::$idFieldsName]);

        $this->beforeUpdate($id, $data);

        $result = $this->query()->where(static::$idFieldsName, $id)->update($data);

        $this->afterUpdate($id, $data, $result);

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

        $this->insertId = $this->query()->add($input);
        if ($this->insertId) {
            $this->setId($this->insertId);
        }

        $this->afterAdd($this->insertId, $input);

        return $this->insertId;
    }

    public function replace()
    {
        $data = $this->all();

        $this->beforeAdd($data);

        $this->insertId = $this->query()->replace($data);

        $this->afterAdd($this->insertId, $data);

        return $this->insertId;
    }

    // 删除一条记录
    public function delete()
    {
        $id = $this->getId();
        if (empty($id)) {
            return false;
        }

        $this->beforeDelete($id);

        $result = $this->query()->where(static::$idFieldsName, $id)->delete();

        $this->afterDelete($id, $result);

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
        fire(
            "{$this->module}.{$this->modelName}.delete.before",
            [&$id]
        );
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
        fire(
            "{$this->module}.{$this->modelName}.delete.after",
            [&$id, $result]
        );
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

    // 新增操作钩子方法，新增前调用
    protected function beforeAdd(array &$input)
    {
        fire(
            "{$this->module}.{$this->modelName}.add.before",
            [&$input]
        );
    }

    // 新增操作钩子方法
    protected function afterAdd($insertId, array &$input)
    {
        fire(
            "{$this->module}.{$this->modelName}.add.after",
            [$insertId, &$input]
        );
    }

    // 修改钩子方法，修改前调用
    protected function beforeUpdate($id, array &$input)
    {
        fire(
            "{$this->module}.{$this->modelName}.update.before",
            [$id, &$input]
        );
    }

    // 修改钩子方法
    protected function afterUpdate($id, array &$input, $result)
    {
        fire(
            "{$this->module}.{$this->modelName}.update.after",
            [$id, &$input, $result]
        );
    }

    /**
     * @return Query
     */
    public function query($name = null)
    {
        return query($name ?: $this->connectionType)->from($this->tableName);
    }

}
