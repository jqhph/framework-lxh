<?php

namespace Lxh\Auth;

use Lxh\Cache\CacheInterface;
use Lxh\Exceptions\Error;
use Lxh\Helper\Util;
use Lxh\Auth\Database\Menu as DefaultMenuModel;

class Menu
{
    /**
     * 保存处理后的菜单数据（开启显示的菜单）
     *
     * @var array
     */
    protected $data;

    /**
     * @var array
     */
    protected $pluginsMenus = [];

    /**
     * 未经处理的原始菜单数据（开启显示的菜单）
     *
     * @var array
     */
    protected $list;

    /**
     * @var DefaultMenuModel
     */
    protected $model;

    /**
     * @var CacheInterface
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
    protected $lifetime = 7776000;

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

    /**
     * @var bool
     */
    protected $useCache = true;

    /**
     * 路由前缀
     *
     * @var string
     */
    protected $routePrefix = '';

    /**
     * @var array
     */
    protected $options = [];

    public function __construct(AuthManager $auth = null, $modelClass = null)
    {
        $this->options = (array)config('admin.menu');

        $this->auth        = $auth ?: auth();
        $this->model       = model($modelClass ?: getnotempty($this->options, 'model', DefaultMenuModel::class));
        $this->useCache    = getvalue($this->options, 'use-cache', true);
        $this->routePrefix = config('admin.route-prefix');

        if ($this->useCache) {
            $this->cache    = cache_factory()->get(getvalue($this->options, 'cache-channel', 'admin-menu'));
            $this->lifetime = getnotempty($this->options, 'lifetime', $this->lifetime);
        }

        fire('menu.resolving', [$this]);
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
            if ($auth && ! $this->auth->can(getvalue($v, 'ability'))) {
                continue;
            }

            if (! isset($v['path'])) {
                if ($v['use_route_prefix']) {
                    $v['path'] = '/' . $this->routePrefix . $v['route'];
                } else {
                    $v['path'] = &$v['route'];
                }
            }

            if ($trans) {
                $v['originName'] = $v['name'];
                $v['name'] = trans_with_global($v['name'], 'menus');
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
     * 注册插件菜单
     *
     * @return $this
     */
    public function addPlugin($content)
    {
        $this->pluginsMenus[] = &$content;

        return $this;
    }

    /**
     * @param $label
     * @param null $url
     * @return string
     */
    public function buildRow($label, $url = null)
    {
        $label = trans($label, 'menus');
        $tab = '';
        if ($url) {
            $id = str_replace('/', '-', $url);
            $tab = "onclick=\"LXHSTORE.TAB.switch('$id', '$url', '$label')\"";
        }

        return "<a $tab>$label</a>";
    }

    /**
     *
     * @return string
     */
    public function renderPlugins()
    {
        $lis = '';

        foreach ($this->pluginsMenus as &$menu) {
            $lis .= '<li>' . $menu . '</li>';
        }

        return $lis;
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

        if ($this->useCache && $data = $this->cache->getArray($this->cacheKey)) {
            $data = $this->makeTree($data);
            // 如果存在子菜单则根据priority字段排序, priority越小越前面
            $this->sort($data);

            return $this->data = $data;
        }

        $this->data = $this->fetch();

        if ($this->useCache) {
            // 缓存数据
            $this->cache->setArray($this->cacheKey, $this->data, $this->lifetime);
        }

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
        if (!$this->useCache) {
            return true;
        }
        $this->flush();

        // 缓存数据
        return $this->cache->setArray($this->cacheKey, $this->fetch(), $this->lifetime);
    }

    /**
     * 清空缓存
     *
     * @return bool
     */
    public function flush()
    {
        if (!$this->useCache) {
            return true;
        }
        return $this->cache->delete($this->cacheKey);
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
        $data = $this->model->findActive();

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
