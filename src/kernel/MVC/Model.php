<?php
namespace Lxh\MVC;

use Lxh\Exceptions\InternalServerError;
use Lxh\Contracts\Container\Container;
use Lxh\Helper\Entity;
use Lxh\ORM\Query;

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
    protected $defaultSelectFields = '*';

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
            $data = $this->query()->select($this->defaultSelectFields)->where($this->idFieldsName, $id)->findOne();
            $this->fill($data);
            return $data;
        }
        return $this->query()->select($this->defaultSelectFields)->find();
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
    protected function getTableName()
    {
        return $this->tableName;
    }

    // 新增操作钩子方法，新增前调用
    protected function beforeAdd(array & $data)
    {

    }

    // 新增操作钩子方法
    protected function afterAdd($insertId, array & $data)
    {

    }

    // 修改钩子方法，修改前调用
    protected function beforeSave($id, array & $data)
    {

    }

    // 修改钩子方法
    protected function afterSave($id, array & $data, $result)
    {

    }

    /**
     * @return Query
     */
    protected function query($name = 'primary')
    {
        return query($name)->from($this->getTableName());
    }

}
