<?php
namespace Lxh\Admin\Acl;

use Lxh\Exceptions\Error;
use Lxh\Helper\Util;
use Lxh\Kernel\AdminUrlCreator;

class Menu
{
    /**
     * 保存处理后的菜单数据
     *
     * @var array
     */
    protected $data;

    /**
     * 未经处理的原始菜单数据
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

    public function __construct()
    {
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

        $last = trans_with_global($this->current['name'], 'menu');
//        if ($this->current['layer'] == 1) {
        return $last;
//        }

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

        $nav = "<a href='$url'>" . trans_with_global($select['name'], 'menu') . "</a>";

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
     * 获取所有的按层级排序好的菜单
     *
     * @return array
     */
    public function all()
    {
        $list = query()->from('menu')->where(['deleted' => 0])->read();

        $data = $this->makeTree($list);

        $this->sort($data);

        return $data;
    }

    protected function & makeTree(& $data, & $id = 0, $level = 1)
    {
        if ($level > 4) {
            throw new Error("Maximum function nesting level of '$level' reached, aborting!");
        }

        $tree = [];
        foreach ($data as & $v) {
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
     *
     * @return array
     */
    public function get()
    {
        if ($this->data) {
            return $this->data;
        }

        $this->data = $this->list = query()->from('menu')->where(['deleted' => 0, 'show' => 1])->read();

        $this->data = $this->makeTree($this->data);

        // 如果存在子菜单则根据priority字段排序, priority越小越前面
        $this->sort($this->data);

        return $this->data;
    }


    protected function sort(array & $lst, $key = 'priority')
    {
        foreach ($lst as & $r) {

            if (! isset($r['subs'])) {
                continue;
            }
            // 按$key字段值正序排序
            Util::quickSort($r['subs'], $key, 0, count($r['subs']) - 1);
            // 递归排序子菜单的子菜单
            $this->sort($r['subs'], $key);
        }
    }

}
