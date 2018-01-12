<?php

namespace Lxh\Auth;

use Lxh\Admin\Admin;
use Lxh\Auth\AuthManager;
use Lxh\Cache\File;
use Lxh\Exceptions\Error;
use Lxh\Helper\Util;

class Menu
{
    /**
     * 保存处理后的菜单数据（开启显示的菜单）
     *
     * @var array
     */
    protected $data;

    /**
     * 未经处理的原始菜单数据（开启显示的菜单）
     *
     * @var array
     */
    protected $list;

    /**
     * 当前菜单数据
     *
     * @var array
     */
    protected $current;

    /**
     * @var \Lxh\Admin\Models\Menu
     */
    protected $model;

    /**
     * @var File
     */
    protected $cache;

    /**
     * @var string
     */
    protected $cacheKey = 'list';

    /**
     * 缓存90天
     *
     * @var int
     */
    protected $expireTime = 7776000;

    /**
     * @var AuthManager
     */
    protected $auth;

    /**
     * @var string
     */
    protected $keyName = 'id';

    /**
     * @var string
     */
    protected $parentKeyName = 'parent_id';

    public function __construct(AuthManager $auth = null)
    {
        $this->auth = $auth ?: auth();
        $this->model = model('Menu');
        $this->cache = File::create('__menu__');
    }

    /**
     * 生成顶部导航条
     *
     * @return string
     */
    public function makeNav()
    {
        if (! $this->current) {
            return trans_with_global('Home');
        }

        return $this->current['name'];
//        $prevUrl = $this->makeUrl(
//            $this->data[$this->current[$this->keyName]]['controller'],
//            $this->data[$this->current[$this->keyName]]['action']
//        );
//
//        return "<a href='$prevUrl'>" . trans_with_global($this->data[$this->current[$this->keyName]]['name'], 'menu') . "</a> / $last";
    }

    /**
     * 根据id获取菜单名称生成导航标题
     *
     * @param  string | int $condition
     * @return string
     */
    public function makeNavByNameOrId($condition)
    {
        $select = [];
        foreach ($this->data as & $m) {
            if ($m['name'] == $condition || $m[$this->keyName] == $condition) {
                $select = & $m;
                break;
            }
        }

        $url = $this->makeUrl($select['controller'], $select['action']);

        $nav = "<a href='$url'>" . $select['name'] . "</a>";

        if ($select[$this->parentKeyName]) {
            $parent = $this->makeNavByNameOrId($select[$this->parentKeyName]);

            return $parent . ' / ' . $nav;
        }

        return $nav;
    }

    /**
     * 根据控制器和action生成url
     *
     * @param $controller
     * @param $action
     * @return string
     */
    public function makeUrl($controller, $action)
    {
        return Admin::url($controller)->action($action);
    }

    /**
     * 判断当前菜单是否被选中
     *
     * @param $controller
     * @param $action
     * @return bool
     */
    public function isActive($controller, $action)
    {
        return __CONTROLLER__ == $controller && strtoupper($action) == strtoupper(__ACTION__);
    }

    /**
     * 生成层级树状数组
     *
     * @param  array $data 要生成层级树状的数组
     * @param  int   $id
     * @param  int   $level
     * @return array
     */
    protected function &makeTree(& $data, $id = 0, $level = 1, $trans = true, $auth = true)
    {
        if ($level > 4) {
            throw new Error("Maximum function nesting level of '$level' reached, aborting!");
        }

        $tree = [];
        foreach ($data as &$v) {
            // 检查用户权限
            if ($auth && ! $this->auth->can(get_value($v, 'ability'))) {
                continue;
            }

            if ($trans) {
                $v['originName'] = $v['name'];
                $v['name'] = trans_with_global($v['name'], 'menus');
            }

            $v['url'] = $this->makeUrl($v['controller'], $v['action']);

            // 存储当前菜单
            if (! $this->current && $this->isActive($v['controller'], $v['action'])) {
                $this->current = $v;
            }

            if ($v[$this->parentKeyName] == $id) {
                $v['subs'] = $this->makeTree($data, $v[$this->keyName], $level + 1, $trans);
                $tree[] = $v;
            }
        }
        return $tree;
    }

    /**
     * 获取所有菜单
     *
     * @return array
     * @throws Error
     */
    public function all()
    {
        $data = $this->fetchList();

        $data = $this->makeTree($data, 0, 1, true, false);

        // 如果存在子菜单则根据priority字段排序, priority越小越前面
        $this->sort($data);

        return $data;

    }


    /**
     * 获取按层级排序好的菜单
     * 只获取显示的菜单
     *
     * @return array
     */
    public function get()
    {
        if ($this->data) {
            return $this->data;
        }

        if ($data = $this->cache->get($this->cacheKey)) {
            $data = $this->makeTree($data);
            // 如果存在子菜单则根据priority字段排序, priority越小越前面
            $this->sort($data);

            return $this->data = $data;
        }

        $this->data = $this->fetch();

        // 缓存数据
        $this->cache->set($this->cacheKey, $this->data, $this->expireTime);

        $this->data = $this->makeTree($this->data);
        $this->sort($this->data);

        return $this->data;
    }

    /**
     * 刷新缓存
     *
     * @return bool
     */
    public function refresh()
    {
        $this->flush();

        // 缓存数据
        return $this->cache->set($this->cacheKey, $this->fetch(), $this->expireTime);
    }

    /**
     * 清空缓存
     *
     * @return bool
     */
    public function flush()
    {
        return $this->cache->flush();
    }

    /**
     * @return array
     */
    public function fetchList()
    {
        if ($this->list) {
            return $this->list;
        }

        return $this->list = $this->model->find();
    }

    /**
     * @return array
     */
    public function fetch()
    {
        $data = $this->model->findShow();

        return $data;
    }

    /**
     * 按$key字段值给层级树状数组正序排序
     *
     * @param  array  $lst 层级树状数组
     * @param  string $key 排序依据字段键名
     * @return array
     */
    protected function sort(array & $lst, $key = 'priority')
    {
        // 顶级菜单排序
        Util::quickSort($lst, $key, 0, count($lst) - 1);

        // 子菜单排序
        foreach ($lst as & $r) {
            if (empty($r['subs'])) {
                continue;
            }

            // 递归排序子菜单的子菜单
            $this->sort($r['subs'], $key);
        }
    }

}
