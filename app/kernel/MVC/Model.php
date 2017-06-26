<?php
namespace Lxh\MVC;

use Lxh\Exceptions\InternalServerError;
use Lxh\Contracts\Container\Container;
use Lxh\Helper\Entity;

class Model extends Entity
{
    /**
     * id字段名称
     *
     * @var string
     */
    protected $idFieldsName = 'id';

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

    public function __construct($name, Container $container)
    {
        $this->modelName = $this->tableName = $name;

        $this->container = $container;

        parent::__construct([]);
    }

    // 查找数据
    public function find()
    {
        $id = $this->{$this->idFieldsName};

        if ($id) {
            $data = $this->query()->where($this->idFieldsName, $id)->findOne();
            $this->fill($data);
            return $data;
        }
        return $this->query()->find();
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

        $result = $this->query()->where($this->idFieldsName, $data[$this->idFieldsName])->update($data);
        if ($result) {
            $this->afterSave($data[$this->idFieldsName], $data);
        }
        return $result;
    }

    // 新增一条记录
    public function add()
    {
        $data = $this->all();

        $this->beforeAdd($data);

        $this->insertId = $this->query()->add($data);
        if ($this->insertId) {
            $this->afterAdd($this->insertId, $data);
        }

        return $this->insertId;
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
    protected function getTableName()
    {
        return $this->tableName;
    }

    // 新增操作钩子方法，新增前调用
    protected function beforeAdd(array & $data)
    {

    }

    // 新增操作钩子方法，新增成功后调用
    protected function afterAdd($insertId, array & $data)
    {

    }

    // 修改钩子方法，修改前调用
    protected function beforeSave($id, array & $data)
    {

    }

    // 修改钩子方法，修改成功后调用
    protected function afterSave($id, array & $data)
    {

    }

    /**
     * @return \Lxh\ORM\Query
     */
    protected function query()
    {
        return $this->container->make('query')->from($this->getTableName());
    }

}
