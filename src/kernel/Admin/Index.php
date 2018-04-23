<?php

namespace Lxh\Admin;

use Closure;
use Lxh\Admin\Layout\Content;
use Lxh\Contracts\Support\Renderable;

/**
 * Class Index.
 */
class Index implements Renderable
{
    /**
     * @var array
     */
    protected $views = [
        'index'   => 'admin::index',
        'top-bar' => 'admin::index.top-bar',
        'sitebar' => 'admin::index.left-bar',
        'user'    => 'admin::index.user'
    ];

    /**
     * @var array
     */
    protected $variables = [
        'maxTab'        => 10,
        'topbarContent' => '',
        'users'         => '',
        'menuTitle'     => '',
        'homeUrl'       => '/index/action/dashboard'
    ];

    /**
     * tab按钮最大数量
     *
     * @var int
     */
    protected $maxTab = 10;

    /**
     * @var Content
     */
    protected $content;

    /**
     * @var mixed
     */
    protected $user;

    /**
     * @var mixed
     */
    protected $topbarContent = [];

    /**
     * 快捷菜单
     *
     * @var array
     */
    protected $contextMenus = [];

    /**
     * @var bool
     */
    protected $useGlobalSearchInput = false;

    public function __construct(Closure $content = null)
    {
        if ($content) {
            $content($this);
        }

        $this->maxTab = config('admin.index.max-tab', 10);

        Admin::css('@lxh/css/bootstrap.min');
    }

    /**
     * 设置首页链接
     *
     * @param $url
     * @return $this
     */
    public function setHomeUrl($url)
    {
        $this->variables['homeUrl'] = $url;
        return $this;
    }

    /**
     * 设置后台主页视图
     *
     * @param $view
     * @return $this
     */
    public function setIndexView($view)
    {
        $this->views['index'] = $view;
        return $this;
    }

    /**
     * 设置顶部工具栏视图
     *
     * @param $view
     * @return $this
     */
    public function setTopbarView($view)
    {
        $this->views['top-bar'] = $view;
        return $this;
    }

    /**
     * 设置菜单栏视图
     *
     * @param $view
     * @return $this
     */
    public function setSitebarView($view)
    {
        $this->views['sitebar'] = $view;
        return $this;
    }

    /**
     * 设置菜单栏用户区块视图
     *
     * @param $view
     * @return $this
     */
    public function setUserView($view)
    {
        $this->views['user'] = $view;
        return $this;
    }

    /**
     * 设置菜单标题
     *
     * @param $title
     * @return $this
     */
    public function setMenuTitle($title)
    {
        $this->variables['menuTitle'] = $this->normalizeContent($title);

        return $this;
    }

    /**
     * 设置tab按钮最大数量
     *
     * @param int $num
     * @return $this
     */
    public function setMaxTab($num)
    {
        $this->maxTab = $num;
        return $this;
    }

    /**
     * 使用全局搜索
     *
     * @return $this
     */
    public function allowGlobalSearchInput()
    {
        $this->useGlobalSearchInput = true;
        return $this;
    }

    /**
     * 定义左边菜单栏用户信息
     * 如果调用了此方法，user视图将不会再渲染
     *
     * @param $content
     * @return $this
     */
    public function setUser($content)
    {
        $this->user = $this->normalizeContent($content);

        return $this;
    }

    /**
     * 设置顶部工具栏右边内容
     *
     * @param $content
     * @return $this
     */
    public function appendTopbarContent($content)
    {
        $this->topbarContent[] = $this->normalizeContent($content);
        return $this;
    }

    /**
     * 增加行内容
     *
     * @param $row
     * @return Content
     */
    public function content($row)
    {
        $this->content[] = &$row;

        return $this;
    }

    protected function normalizeContent(&$content)
    {
        if ($content instanceof Closure) {
            $content = $content($this);
        } elseif($content instanceof Renderable) {
            $content = $content->render();
        }
        return $content;
    }

    protected function buildUser()
    {
        $user = __admin__();
        $name = $user->first_name . ' ' . $user->last_name;
        $username = $name ?: $user->username;

        $avatar = '';
        if (method_exists($user, 'avatar')) {
            $avatar = $user->avatar();
        }

        $avatar = $avatar ?: admin_img('/images/users/avatar-1.jpg');

        $this->topbarContent[] = view($this->views['user'], ['name' => $username, 'avatar' => &$avatar])->render();
    }

    protected function buildTopbar()
    {
        $this->buildUser();

        return view(
            $this->views['top-bar'],
            [
                'content' => implode('', $this->topbarContent),
                'useGlobalSearchInput' => $this->useGlobalSearchInput,
            ]
        )->render();
    }

    protected function buildSitebar()
    {
        return view(
            $this->views['sitebar'],
            [
                'users' =>'',
                'title' => $this->variables['menuTitle'],
                'home' => $this->variables['homeUrl'],
                'menu' => auth()->menu(),
            ]
        )->render();
    }

    /**
     * 增加快捷菜单
     *
     * @param string $content 主菜单内容
     * @param array $children 子菜单内容
     * @return $this
     */
    public function contextMenu($content, array $children = [])
    {
        $this->contextMenus[] = [
            'text'     => &$content,
            'children' => &$children
        ];

        return $this;
    }

    /**
     * @return array
     */
    protected function variables()
    {
        $contents = '';
        if ($this->content) {
            foreach ($this->content as &$content) {
                $contents .= $this->normalizeContent($content);
            }
        }

        return array_merge($this->variables, [
            'sitebar'      => $this->buildSitebar(),
            'topbar'       => $this->buildTopbar(),
            'content'      => &$content,
            'contextMenus' => &$this->contextMenus
        ]);
    }

    public function render()
    {
        Admin::includeHelpers();

        return view($this->views['index'], $this->variables())->render();
    }

}
