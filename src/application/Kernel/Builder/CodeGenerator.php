<?php
/**
 * 代码生成器入口
 *
 * @author Jqh
 * @date   2017/7/21 10:45
 */

namespace Lxh\Kernel\Builder;

use Lxh\Kernel\Builder\Driver\Fields;

class CodeGenerator
{
    /**
     * 错误提示信息
     *
     * @var string
     */
    protected $errorMsg;

    /**
     * @var Fields
     */
    protected $fields;

    /**
     * 生成器
     *
     * @var array
     */
    protected $creators = [];

    protected $recoreds = [];

    /**
     * 错误提示信息数组
     *
     * @var array
     */
    protected $tips = [
        'required' => 'The %s field is required',
        'lengthMin' => 'The %s field must contain greater than %d value',
        'fieldsError' => 'Invalid fields arguments',
        'exists' => '%s already exists',
        'default' => 'Unknown Error'
    ];

    public function __construct()
    {
        $this->fields = $this->creator('Fields');
    }

    /**
     * 生成器入口
     * 
     * @param  array $options 生成器配置
     *
     *  {
            "controller_name":"Test",
            "en_name":"Test",
            "zh_name":"\u6d4b\u8bd5\u6a21\u5757",
            "author":"Jqh",
            "actions":["add","update","delete","list","search","order","displayMenu"],
            "icon":"","limit":"20",
            "field_name":["name"],
            "field_en_name":["name"],
            "field_zh_name":["\u540d\u79f0"],
            "field_default":[""],
            "fieldsName":["name"],
            "rank":["0"],
            "sorting":["1"],
            "search":["1"],
            "list":["1"],
            "module":"Admin",
            "inheritance":"Lxh\\Kernel\\Controller\\Record",
            "field_type":["varchar"],
            "group":["primary"]
        }
     *
     *
     * @return bool
     */
    public function make(array & $options)
    {
        // 配置验证
        if (! $this->optionsValidate($options)) {
            return false;
        }

        // 生成字段配置信息
        $this->fields()->make($options);

        return $this->fields;

        // 生成控制器
        $this->creator('Controller')->make($options);
        
        // 生成模型
        $this->creator('Model')->make($options);

        // 生成List模板
        $this->creator('ListView')->make($options);

        // 生成Detail模板
        $this->creator('DetailView')->make($options);

        // 生成语言包
        $this->creator('Language')->make($options);

        // 打包
        $this->creator('FilePack')->make($options);

        // 安装
        if (! $this->creator('Install')->make($options)) {
            $this->rollback(['Controller', 'Model', 'View', 'Language', 'Fields', 'FilePack']);
            return false;
        }

        // 生成数据表
        if (! $this->creator('Database')->make($options)) {
            $this->rollback(['Controller', 'Model', 'View', 'Language', 'Fields', 'FilePack', 'Install']);
            return false;
        }

        return true;
    }

    public function module()
    {
        return __MODULE__;
    }

    /**
     * @return Fields
     */
    public function fields()
    {
        return $this->fields;
    }

    /**
     * 回滚操作
     *
     * @return void
     */
    public function rollback($names)
    {
        foreach ((array) $names as & $name) {
            $this->creator($name)->rollback();

        }
    }

    /**
     * 获取生成器
     *
     * @param  string $name
     * @return Driver\Creator
     */
    public function creator($name)
    {
        if (isset($this->creators[$name])) {
            return $this->creators[$name];
        }
        $class = "Lxh\\Kernel\\Builder\\Driver\\$name";

        return $this->creators[$name] = new $class($this);
    }

    /**
     * 配置参数验证
     *
     *
     * @param  array $options 生成器配置
     * @return bool 验证通过返回true，否则返回false
     */
    public function optionsValidate(array & $options)
    {
        if (empty($options['controller_name'])) {
            $this->setError('required', ['controller_name']);
            return false;
        }

        if (empty($options['field_name'])) {
            $this->setError('required', ['field_name']);
            return false;
        }

        if (empty($options['field_type'])) {
            $this->setError('required', ['field_type']);
            return false;
        }

        if (empty($options['group'])) {
            $this->setError('required', ['group']);
            return false;
        }

        if (empty($options['inheritance'])) {
            $this->setError('required', ['inheritance']);
            return false;
        }

        $fieldsNameTotal = count($options['field_name']);

        if ($fieldsNameTotal < 1) {
            $this->setError('lengthMin', ['field_name', 1]);
            return false;
        }

        if (
            ! ($fieldsNameTotal == count($options['field_en_name']) && $fieldsNameTotal == count($options['field_zh_name'])
            && $fieldsNameTotal == count($options['field_default']) && $fieldsNameTotal == count($options['rank'])
            && $fieldsNameTotal == count($options['sorting']) && $fieldsNameTotal == count($options['search'])
            && $fieldsNameTotal == count($options['list']) && $fieldsNameTotal == count($options['field_type'])
            && $fieldsNameTotal == count($options['group']))
        ) {
            $this->setError('fieldsError');
            return false;
        }

        return true;
    }

    /**
     * 保存或获取记录
     *
     * @param  string $name
     * @return mixed
     */
    public function record($name = null, $data = null)
    {
        if ($data === null) {
            if ($name === null) {
                return $this->recoreds;
            }
            return isset($this->recoreds[$name]) ? $this->recoreds[$name] : null;
        }
        $this->records[$name] = & $data;
    }

    /**
     * 获取错误信息（已翻译）
     *
     * @return string
     */
    public function errors()
    {
        return $this->errorMsg;
    }

    /**
     * 设置错误信息
     *
     * @param  mixed $type 错误类型
     * @param  array $sprints 需要插入到格式化翻译字符的参数
     * @return void
     */
    public function setError($type, array $sprints = [])
    {
        if (isset($this->tips[$type])) {
            $this->errorMsg = trans($this->tips[$type], 'tip', $sprints);
            return;
        }

        $this->errorMsg = trans($this->tips['default'], 'tip', $sprints);
    }



}
