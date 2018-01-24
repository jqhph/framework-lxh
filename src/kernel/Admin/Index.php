<?php

namespace Lxh\Admin;

use Closure;
use Lxh\Admin\Layout\Content;
use Lxh\Admin\Layout\Row;
use Lxh\Admin\Widgets\Navbar;
use Lxh\Contracts\Support\Renderable;
use Lxh\MVC\Model;
use InvalidArgumentException;

/**
 * Class Index.
 */
class Index
{
    /**
     * @var array
     */
    protected $views = [
        'index' => 'admin::index',
        'top-bar' => 'admin::index.top-bar',
        'sitebar' => 'admin::index.left-bar',
        'user' => 'admin::index.user'
    ];

    /**
     * @var array
     */
    protected $variables = [
        'maxTab' => 10,
        'topbarContent' => '',
        'users' => '',
        'menuTitle' => '',
        'homeUrl' => '/admin/index/action/dashboard'
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
    protected $topbarContent;

    /**
     * 刷新按钮
     *
     * @var bool
     */
    protected $allowRefresh = true;

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
    }

    /**
     * 禁用刷新按钮
     *
     * @return $this
     */
    public function disableRefresh()
    {
        $this->allowRefresh = false;
        return $this;
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
    public function addTopbarContent($content)
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
    public function row($row)
    {
        return $this->content()->row($row);
    }

    /**
     * @return Content
     */
    public function content()
    {
        if (! $this->content) {
            $this->content = new Content();
        }

        return $this->content;
    }

    protected function normalizeContent(&$content)
    {
        if ($content instanceof Closure) {
            $this->users = $content($this);
        } elseif($content instanceof Renderable) {
            $this->users = $content->render();
        }
        return (string) $content;
    }

    protected function buildUser()
    {
        if ($this->user) {
            return $this->user;
        }

        $user = admin();
        $name = $user->first_name . $user->last_name;
        $username = $name ?: $user->username;

        $avatar = $user->avatar() ?: admin_img('/images/users/avatar-1.jpg');

        return view($this->views['user'], ['name' => $username, 'avatar' => &$avatar])->render();

    }

    protected function buildTopbar()
    {
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
                'users' => $this->buildUser(),
                'title' => $this->variables['menuTitle'],
                'home' => $this->variables['homeUrl'],
                'menu' => auth()->menu(),
            ]
        )->render();
    }

    protected function variables()
    {
        $content = '';
        if ($this->content) {
            $content = $this->content->build();
        }

        if ($this->allowRefresh) {
            $this->topbarContent[] = <<<EOF
<li><div class="notification-box"><ul class="list-inline m-b-0"><li><a onclick="IFRAME.reload()" class="right-bar-toggle"><i class="zmdi zmdi-refresh-alt"></i></a></li></ul></div></li>
EOF;
        }

        return array_merge($this->variables, [
            'topbar' => $this->buildTopbar(),
            'sitebar' => $this->buildSitebar(),
            'content' => &$content,
        ]);
    }

    public function render()
    {
        return view($this->views['index'], $this->variables())->render();
    }

}
