<?php
/**
 * 语言包管理
 *
 * @author Jqh
 * @date   2017/6/29 20:30
 */

namespace Lxh\Language;

use Lxh\Helper\Entity;
use Lxh\Contracts\Container\Container;

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
     * @param
     * @return void
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
     * Translate label/labels
     *
     * @param  string $label name of label
     * @param  string $category
     * @param  mixed $default
     * @return string | array
     */
    public function translate($label, $category = 'labels')
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

        return $translated ?: $label;
    }

    /**
     * 使用全局语言包翻译
     *
     * @param  string $label 需要翻译的名称
     * @param  string $category 翻译的类型  
     * @return void
     */
    public function translateWithGolobal($label, $category = 'labels')
    {
        if (! isset($this->packages[$this->language])) {
            return $label;
        }
        return $this->packages[$this->language]->get("Global.{$category}.$label", $label);
    }

    /**
     * 选项翻译
     *
     * @param  string|int $value 选项值
     * @param  string     $value 选项名称
     * @return string|int
     */
    public function translateOption($value, $label)
    {
        if (! isset($this->packages[$this->language])) {
            return $value;
        }

        $options = $this->packages[$this->language]->get($this->scopeName. '.options.' . $label);

        return isset($options[$value]) ? $options[$value] : $value;
    }

    /**
     * Save changes
     *
     * @return bool
     */
    public function save()
    {
//        $paths = $this->paths['customPath'];
//        $currentLanguage = $this->getLanguage();
//
//        $result = true;
//        if (!empty($this->changedData)) {
//            foreach ($this->changedData as $scope => & $data) {
//                if (!empty($data)) {
//                    $result &= $this->fileManager->mergeContents(array($path, $currentLanguage, $scope.'.php'), $data, true);
//                }
//            }
//        }
//
//        if (!empty($this->deletedData)) {
//            foreach ($this->deletedData as $scope => & $unsetData) {
//                if (!empty($unsetData)) {
//                    $result &= $this->fileManager->unsetContents(array($path, $currentLanguage, $scope.'.php'), $unsetData, true);
//                }
//            }
//        }
//
//        if ($result == false) {
//            throw new Error("Error saving languages. See log file for details.");
//        }
//
//        $this->clearChanges();
//
//        return (bool) $result;
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
     * Get data of Unifier language files
     *
     * @return array
     */
    protected function getData()
    {
        $currentLanguage = $this->getLanguage();
        if (!isset($this->data[$currentLanguage])) {

        }

        return $this->data[$currentLanguage];
    }

    /**
     * Set/change a label
     *
     * @param string $scope
     * @param string $category
     * @param string | array $name
     * @param mixed $value
     *
     * @return void
     */
    public function set($scope, $category, $name, $value)
    {
        if (is_array($name)) {
            foreach ($name as $rowLabel => & $rowValue) {
                $this->set($scope, $category, $rowLabel, $rowValue);
            }
            return;
        }

        $this->changedData[$scope][$category][$name] = & $value;

        $this->undelete($scope, $category, $name);
    }

    /**
     * Remove a label
     *
     * @param  string $name
     * @param  string $category
     * @param  string $scope
     *
     * @return void
     */
    public function delete($scope, $category, $name)
    {
        if (is_array($name)) {
            foreach ($name as $rowLabel) {
                $this->delete($scope, $category, $rowLabel);
            }
            return;
        }

        $this->deletedData[$scope][$category][] = $name;

        $currentLanguage = $this->getLanguage();
        if (!isset($this->data[$currentLanguage])) {

        }

        if (isset($this->data[$currentLanguage][$scope][$category][$name])) {
            unset($this->data[$currentLanguage][$scope][$category][$name]);
        }

        if (isset($this->changedData[$scope][$category][$name])) {
            unset($this->changedData[$scope][$category][$name]);
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
