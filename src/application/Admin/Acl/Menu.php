<?php
namespace Lxh\Admin\Acl;

use Lxh\Exceptions\Error;
use Lxh\Helper\Util;
use Lxh\Kernel\AdminUrlCreator;

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
     * @var \Lxh\Admin\Model\Menu
     */
    protected $model;

    public function __construct()
    {
        $this->model = model('Menu');

        $this->get();
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

        // $last = trans_with_global($this->current['name'], 'menu');

        return $this->current['name'];

        $prevUrl = $this->makeUrl($this->data[$this->current['id']]['controller'], $this->data[$this->current['id']]['action']);

        return "<a href='$prevUrl'>" . trans_with_global($this->data[$this->current['id']]['name'], 'menu') . "</a> / $last";
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
        foreach ($this->list as & $m) {
            if ($m['name'] == $condition || $m['id'] == $condition) {
                $select = & $m;
                break;
            }
        }

        $url = $this->makeUrl($select['controller'], $select['action']);

        $nav = "<a href='$url'>" . $select['name'] . "</a>";

        if ($select['parent_id']) {
            $parent = $this->makeNavByNameOrId($select['parent_id']);

            return $parent . ' / ' . $nav;
        }

        return $nav;
    }

    /**
     * 根据控制器和action生成url
     *
     * @param $controller
     * @param $action
     * @return string url
     */
    public function makeUrl($controller, $action)
    {
        return AdminUrlCreator::makeAction($action, $controller);
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
     * 权限列表
     *
     * @return array
     */
    public function permissionsList()
    {
        $list = $this->all();

        // 每行显示选项数量
        $colspan = 6;

        $data = [];
        $others = [
            'rows' => [[],]
        ];

        $hasTop = false;

        foreach ($list as & $r) {
            if (! empty($r['subs'])) {
                // 有子菜单，合并所有子菜单
                array_push($data, ['title' => $r['originName'], 'rows' => $this->mergeTree($r['subs'], $colspan)]);
                continue;
            }
            if (empty($r['action']) || empty($r['controller'])) {
                continue;
            }

            $hasTop = true;
            // 没有子菜单的情况
            // 每个row最多包含6个数组
            $this->makeItem($others['rows'], $r, $colspan);
        }

        if ($hasTop) {
            array_push($data, $others);
        }

        return $data;
    }

    // 没有子菜单的情况
    // 每个row最多包含6个数组
    protected function makeItem(& $items, & $record, $colspan = 6)
    {
        foreach ($items as & $row) {
            if (count($row) < $colspan) break;
        }
        if (count($row) >= $colspan) {
            // 大于6个，则创建一个新的数组
            array_push($items, [['name' => $record['originName'], 'value' => $record['id']]]);
        } else {
            // 小于6个直接push
            array_push($row, ['name' => $record['originName'], 'value' => $record['id']]);
        }
    }

    /**
     * 合并树状数组为普通数组
     *
     * @param  array $tree
     * @return array
     */
    public function mergeTree(& $tree, $colspan = 6)
    {
        $items = [[]];

        foreach ($tree as & $r) {
            // 没有子菜单的情况
            // 每个row最多包含6个数组
            if (! empty($r['action']) && ! empty($r['controller'])) {
                $this->makeItem($items, $r, $colspan);
            }

            if (! empty($r['subs'])) {
                // 有子菜单，合并所有子菜单
                $items = array_merge($items, $this->mergeTree($r['subs'], $colspan));
            }
        }

        return $items;
    }

    /**
     * 获取所有的按层级排序好的菜单
     *
     * @return array
     */
    public function all()
    {
        $list = $this->model->find();

        $data = $this->makeTree($list);

        $this->sort($data);

        return $data;
    }

    /**
     * 生成层级树状数组
     *
     * @param  array $data 要生成层级树状的数组
     * @param  int   $id
     * @param  int   $level
     * @return array
     */
    protected function & makeTree(& $data, & $id = 0, $level = 1)
    {
        if ($level > 4) {
            throw new Error("Maximum function nesting level of '$level' reached, aborting!");
        }

        $tree = [];
        foreach ($data as & $v) {
            $v['originName'] = $v['name'];
            $v['name'] = trans_with_global($v['name'], 'menus');

            $v['url'] = $this->makeUrl($v['controller'], $v['action']);

            // 存储当前菜单
            if (! $this->current && $this->isActive($v['controller'], $v['action'])) {
                $this->current = $v;
            }

            if ($v['parent_id'] == $id) {
                $v['subs'] = $this->makeTree($data, $v['id'], $level + 1);
                $tree[] = $v;
            }
        }
        return $tree;
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

        $this->data = $this->list = $this->model->findShow();

        $this->data = $this->makeTree($this->data);

        // 如果存在子菜单则根据priority字段排序, priority越小越前面
        $this->sort($this->data);

        return $this->data;
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
