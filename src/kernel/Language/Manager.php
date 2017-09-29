<?php
/**
 * 语言包管理
 *
 * @author Jqh
 * @date   2017/6/29 20:30
 */

namespace Lxh\Language;

use Lxh\File\FileManager;
use Lxh\Helper\Entity;
use Lxh\Contracts\Container\Container;
use Lxh\Helper\Util;

class Manager
{
    protected $container;

    /**
     * 语言包数组
     *
     * @var array
     */
    protected $packages = [];

    private $deletedData = array();

    private $changedData = array();

    private $root = __ROOT__;

    private $language = null;

    protected $defaultLanguage = 'en';

    /**
     * 已载入模块的语言包
     * 'en' => []
     *
     * @var array
     */
    protected $loadedScopes = [

    ];

    /**
     * 语言包路径
     *
     * @var string
     */
    private $dir = 'config/language';

    /**
     * 语言包类型数组
     *
     * @var array
     */
    protected $categories = [
        'labels',
        'fields',
        'options',
    ];

    /**
     * 项目模块名称
     *
     * @var string
     */
    protected $moduleName;

    /**
     * 语言包模块名称
     *
     * @var string
     */
    protected $scopeName;

    public function __construct(Container $container)
    {
        $this->container = $container;

        $this->language = config('language', $this->defaultLanguage);

        $c = $container->make('controller.manager');

        $this->moduleName = $c->moduleName();

    }

    /**
     * 设置语言包模块
     *
     * @param string $name
     * @return static
     */
    public function scope($name)
    {
        $this->scopeName = $name;

        $this->loadPackage($name);

        return $this;
    }

    /**
     * 获取所有已加载的语言包数据
     *
     * @return array
     */
    public function all()
    {
        $data = [];
        foreach ($this->packages as $lang => & $entity) {
            $data[$lang] = $entity->all();
        }
        return $data;
    }

    public function toJson()
    {
        return json_encode($this->all());
    }

    /**
     * 设置语言类型
     *
     * @param
     * @return void
     */
    public function type($lang)
    {
        $this->language = $lang;
    }

    /**
     * 载入语言包
     *
     * @param  string $scope  语言包模块名称
     * @param  bool   $reload 是否重新载入
     * @return mixed
     */
    public function loadPackage($scope, $lang = null, $reload = false)
    {
        $lang = $lang ?: $this->language;
        if (! $reload && isset($this->loadedScopes[$lang][$scope])) {
            return false;
        }
        // 获取语言包路径
        $file = $this->getPackagePath($scope, $lang);

        $result = false;

        if (is_file($file)) {
            if (! isset($this->packages[$lang])) {
                // 初始化语言包
                $this->packages[$lang] = new Entity();
            }

            $result = true;
            // 保存
            $this->packages[$lang]->$scope = (array) include $file;
        }

        // 记录语言包载入结果
        $this->loadedScopes[$lang][$scope] = $result;
    }

    /**
     * 获取语言包数据
     *
     * @param  array  $scopes
     * @param  string $lang
     * @return array
     */
    public function getPackages(array $scopes, $lang = null)
    {
        $lang = $lang ?: $this->language;

        if ($scopes) {
            foreach ($scopes as & $s) {
                $this->loadPackage($s, $lang);
            }
        }

        $data = [];

        $data[$lang] = $this->packages[$lang]->all();

        if (! in_array('Global', $scopes)) {
            unset($data[$lang]['Global']);
        }

        return $data;
    }

    /**
     * 获取语言包路径
     *
     * @param  string $scope
     * @return string
     */
    public function getPackagePath($scope, $lang = null)
    {
        $lang = $lang ?: $this->language;
        if ($scope == 'Global') {
            return "{$this->root}{$this->dir}/{$lang}/$scope.php";
        }
        return "{$this->root}{$this->dir}/{$lang}/{$this->moduleName}/$scope.php";
    }

    /**
     * 获取根目录
     */
    public function getBasePath()
    {
        return "{$this->root}{$this->dir}/";
    }


    /**
     * Translate label/labels
     *
     * @param  string $label name of label
     * @param  string $category
     * @param  mixed $default
     * @param  array $sprints format fields array
     * @return string | array
     */
    public function translate($label, $category = 'labels', array $sprints = [])
    {
        if (! isset($this->packages[$this->language])) {
            return $label;
        }

        if (is_array($label)) {
            $translated = array();

            foreach ($label as & $subLabel) {
                $translated[$subLabel] = $this->translate($subLabel, $category);
            }

            return $translated;
        }
        //console_error("{$this->scopeName}.{$category}.$label");
        $translated = $this->packages[$this->language]->get("{$this->scopeName}.{$category}.$label");

        if (empty($translated)) {
            $translated = $this->packages[$this->language]->get("Global.{$category}.$label");
        }

        $result = $translated ?: $label;

        if ($sprints && $result) {
            $result = vsprintf($result, $sprints);
        }

        return $result;
    }

    /**
     * @return FileManager
     */
    protected function files()
    {
        return make('files');
    }

    /**
     * 使用全局语言包翻译
     *
     * @param  string $label 需要翻译的名称
     * @param  string $category 翻译的类型
     * @param  array  $sprints 需要插入到格式化翻译字符的参数
     * @return string
     */
    public function translateWithGolobal($label, $category = 'labels', array $sprints = [])
    {
        if (! isset($this->packages[$this->language])) {
            return $label;
        }
        $translated = $this->packages[$this->language]->get("Global.{$category}.$label", $label);
        if ($sprints && $translated) {
            $translated = vsprintf($translated, $sprints);
        }

        return $translated;
    }

    /**
     * 选项翻译
     *
     * @param  string|int $value 选项值
     * @param  string     $value 选项名称
     * @return string|int
     */
    public function translateOption($value, $field)
    {
        if (! isset($this->packages[$this->language])) {
            return $value;
        }

        $options = $this->packages[$this->language]->get($this->scopeName. '.options.' . $field);

        return isset($options[$value]) ? $options[$value] : $value;
    }

    /**
     * Save changes
     *
     * @return bool
     */
    public function save()
    {
        $file = $this->files();

        foreach ($this->changedData as $scope => & $data) {
            if (!empty($data)) {
                $path = $this->getPackagePath($scope);

                if (! $file->mergePhpContents($path, $data)) {
                    return false;
                }
            }
        }

        foreach ($this->deletedData as $scope => & $unsetData) {
            if (!empty($unsetData)) {
                $path = $this->getPackagePath($scope);

                $data = [$unsetData];

                if (! $file->unsetContents($path, $data, true)) {
                    return false;
                }
            }
        }

        $this->clearChanges();

        return true;
    }

    /**
     * Clear unsaved changes
     *
     * @return void
     */
    public function clearChanges()
    {
        $this->changedData = array();
        $this->deletedData = array();

    }

    /**
     * Set/change a label
     *
     * @param string $scope
     * @param string $category
     * @param string | array $labelName
     * @param mixed $value
     *
     * @return void
     */
    public function set($scope, $category, $labelName, $value)
    {
        if (is_array($labelName)) {
            foreach ($labelName as $rowLabel => & $rowValue) {
                $this->set($scope, $category, $rowLabel, $rowValue);
            }
            return;
        }

        $this->changedData[$scope][$category][$labelName] = & $value;

        $this->undelete($scope, $category, $labelName);
    }

    /**
     * Remove a label
     *
     * @param  string $scope
     * @param  string $category
     * @param  string $labelName
     *
     * @return void
     */
    public function delete($scope, $category, $labelName, $value = null)
    {
        if (is_array($labelName)) {
            foreach ($labelName as $k => & $rowLabel) {
                $this->delete($scope, $category, $rowLabel);
            }
            return;
        }

        if ($value) {
            $this->deletedData[$scope][$category][$labelName] = (array) $value;
        } else {
            $this->deletedData[$scope][$category][] = & $labelName;
        }

        if (isset($this->changedData[$scope][$category][$labelName])) {
            unset($this->changedData[$scope][$category][$labelName]);
        }
    }

    protected function undelete($scope, $category, $name)
    {
        if (isset($this->deletedData[$scope][$category])) {
            foreach ($this->deletedData[$scope][$category] as $key => $labelName) {
                if ($name === $labelName) {
                    unset($this->deletedData[$scope][$category][$key]);
                }
            }
        }
    }

}
