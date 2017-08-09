<?php
/**
 * 字段数组生成器
 *
 * @author Jqh
 * @date   2017/7/21 17 => 14
 */

namespace Lxh\Kernel\Builder\Driver;

use Lxh\Exceptions\Error;
use Lxh\Helper\Entity;
use Lxh\Helper\Util;

class Fields extends Creator
{
    /**
     * 字段名称
     *
     * @var string
     */
    protected $fieldName;

    /**
     * 保存处理好的字段信息
     *
     * @var array
     */
    protected $data = [];
    /**
     * comment
     *
     * @param  array $options
     *   [
     *       "controller_name" => "Test",
     *       "en_name" => "Test",
     *       "zh_name" => "\u6d4b\u8bd5",
     *       "author" => "Jqh",
     *       "actions" => ["add", "update", "delete", "list", "search", "order", "displayMenu"],
     *       "icon" => "",
     *       "limit" => "20",
     *       "field_name" => ["created_at", "name"],
     *       "field_en_name" => ["created at", ""],
     *       "field_zh_name" => ["\u521b\u5efa\u65f6\u95f4", ""],
     *       "field_default" => ["", ""],
     *       "fieldsName" => ["created_at", "name"],
     *       "rank" => ["0", "0"],
     *       "sorting" => ["1", "1"],
     *       "search" => ["1", "1"],
     *       "list" => ["1", "1"],
     *       "inheritance" => "Lxh\\Kernel\\Controller\\Record",
     *       "field_type" => ["varchar", "icon"],
     *       "group" => ["primary", "primary"]
     *   ];
     * @return array
     */
    public function make(array $options)
    {
        $this->setOptions($options);

        foreach ($options['field_name'] as $k => & $name) {
            $ranks = (array) $this->options('rank');
            $sorts = (array) $this->options('sorting');

            $this->data[$name] = new Entity([
                // 字段模板
                'view' => $options['field_type'][$k],
                // 排序优先级，值越小排得越前
                'priority' => get_value($ranks, $k, 0),
                // 是否是排序项
                'isSortItem' => get_value($sorts, $k, 0),
                // 是否是搜索项
                'isSearchItem' => $options['search'][$k],
                // 是否是列表项
                'isListItem' => $options['list'][$k],
                // 分组（预留）
                'group' => $options['group'][$k],
                // 默认值
                'default' => $options['field_default'][$k],
                // 英文名
                'en' => $options['field_en_name'][$k],
                // 中文名
                'zh' => $options['field_zh_name'][$k],
                // 如果是枚举类型字段，应该有选项值
                'options' => $this->normalizeFieldOptions($name, $options),
            ]);
        }
    }

    // 获取字段option值
    protected function normalizeFieldOptions($name, array & $options)
    {
        $valueKey  = "$name-value";
        $enlishKey = "$name-English";
        $zhKey     = "$name-Chinese";

        if (empty($options[$valueKey]) || empty($options[$enlishKey]) || empty($options[$zhKey])) {
            return false;
        }

        $data = [];

        foreach ($options[$valueKey] as $k => & $v) {
            $data[$v] = [
                'English' => get_value($options[$enlishKey], $k),
                'Chinese' => get_value($options[$zhKey], $k),
            ];
        }

        return $data;
    }

    /**
     * 设置字段名称
     *
     * @param  string $name
     * @return static
     */
    public function name($name)
    {
        $this->fieldName = $name;

        return $this;
    }

    /**
     * 获取字段名称列表
     *
     * @return array
     */
    public function nameList()
    {
        return array_keys($this->data);
    }

    /**
     * 获取字段配置信息数组，并排好序
     *
     * @return array
     */
    public function all()
    {
        $data = [];

        foreach ($this->data as $name => & $entity) {
            $tmp = $entity->all();
            $tmp['name'] = $name;

            $data[] = $tmp;
        }

        Util::quickSort($data, 'priority', 0, count($data) - 1);

        return $data;
    }

    // 获取字段配置信息
    public function __get($name)
    {
        if (! $this->fieldName || empty($this->data[$this->fieldName])) return null;

        return $this->data[$this->fieldName]->$name;
    }

    // 获取字段配置信息
    public function get($name, $default = null)
    {
        if (! $this->fieldName || empty($this->data[$this->fieldName])) return null;

        return $this->data[$this->fieldName]->get($name, $default);
    }

    public function preview(array $options)
    {

    }

    public function previewCode(array $options)
    {

    }

}
