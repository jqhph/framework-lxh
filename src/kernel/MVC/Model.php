<?php
namespace Lxh\MVC;

use Lxh\Exceptions\InternalServerError;
use Lxh\Contracts\Container\Container;
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
    protected $idFieldsName = 'id';

    /**
     * 默认查询的字段
     *
     * @var string|array
     */
    protected $selectFields = '*';

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

    protected $connectionType = 'primary';

    /**
     * @var Dispatcher
     */
    protected $events;

    protected $queries = [];

    public function __construct($name, Container $container)
    {
        $this->modelName = $name;

        if (! $this->tableName) $this->tableName = Util::convertWith($name, true);

        $this->container = $container;

        $this->events = $container['events'];

        $this->initialize();
    }

    /**
     * @param $name
     * @return $this
     */
    public function setIdName($name)
    {
        $this->idFieldsName = $name;
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
     * 获取id
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->get($this->idFieldsName);
    }

    /**
     * @return string
     */
    public function getKeyName()
    {
        return $this->idFieldsName;
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
     * select
     *
     * @return Query
     */
    public function select($fields)
    {
        return $this->query()->select($fields);
    }

    // 查找数据
    public function find()
    {
        $id = $this->{$this->idFieldsName};

        if ($id) {
            $data = $this->query()->select($this->selectFields)->where($this->idFieldsName, $id)->findOne();
            $this->fill($data);
            return $data;
        }
        return $this->query()->select($this->selectFields)->find();
    }

    /**
     * 修改操作
     */
    public function save()
    {
        $data = $this->all();

        if (empty($data[$this->idFieldsName])) {
            return false;
        }
        $id = $data[$this->idFieldsName];

        unset($data[$this->idFieldsName]);

        $this->beforeSave($id, $data);

        $result = $this->query()->where($this->idFieldsName, $id)->update($data);

        $this->afterSave($id, $data, $result);

        return $result;
    }

    // 新增一条记录
    public function add()
    {
        $data = $this->all();

        $this->beforeAdd($data);

        $this->insertId = $this->query()->add($data);

        $this->afterAdd($this->insertId, $data);

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
        $id = $this->id;
        if (empty($id)) {
            return false;
        }

        $this->beforeDelete($id);

        $result = $this->query()->where($this->idFieldsName, $id)->delete();

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

    // 新增操作钩子方法，新增前调用
    protected function beforeAdd(array &$input)
    {

    }

    // 新增操作钩子方法
    protected function afterAdd($insertId, array &$input)
    {

    }

    // 修改钩子方法，修改前调用
    protected function beforeSave($id, array &$input)
    {

    }

    // 修改钩子方法
    protected function afterSave($id, array &$input, $result)
    {

    }

    /**
     * @return Query
     */
    protected function query($name = null)
    {
        if (isset($this->queries[$name])) {
            return $this->queries[$name]->from($this->tableName);
        }

        return $this->queries[$name] = query($name ?: $this->connectionType)->from($this->tableName);
    }

}
