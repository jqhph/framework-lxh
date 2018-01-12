<?php

namespace Lxh\Admin;

use Closure;
use Lxh\Admin\Layout\Content;
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
     * @var string
     */
    protected $view = 'admin::index';

    /**
     * tab按钮最大数量
     *
     * @var int
     */
    protected $maxTab = 10;

    /**
     * 顶部工具栏内容定义
     *
     * @var mixed
     */
    protected $topbar;

    /**
     * 用户信息
     *
     * @var string
     */
    protected $users;

    /**
     * @var Content
     */
    protected $content;

    /**
     * @var string
     */
    protected $menuTitle = '';

    /**
     * @var string
     */
    protected $homeUrl = '/admin/index/index';
    
    public function __construct(Closure $content = null)
    {
        if ($content) {
            $content($this);
        }

        $this->maxTab = config('admin.index.max-tab', 10);
    }

    /**
     * @param $url
     * @return $this
     */
    public function setHomeUrl($url)
    {
        $this->homeUrl = $url;
        return $this;
    }

    /**
     *
     *
     * @param $row
     * @return $this
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

    /**
     * @param $title
     * @return $this
     */
    public function menuTitle($title)
    {
        $this->menuTitle = $this->normalizeContent($title);

        return $this;
    }

    /**
     * 定义左边菜单栏用户信息
     *
     * @param $content
     * @return $this
     */
    public function users($content)
    {
        $this->users = $this->normalizeContent($content);

        return $this;
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

    /**
     * 顶部工具栏内容
     *
     * @param $content
     * @return $this
     */
    public function topbar($content)
    {
        $this->topbar = $this->normalizeContent($content);
        return $this;
    }

    /**
     * 设置tab按钮最大数量
     *
     * @param int $num
     * @return $this
     */
    public function maxTab($num)
    {
        $this->maxTab = $num;
        return $this;
    }
    
    protected function setupUsers()
    {
        $user = admin();
        $name = $user->first_name . $user->last_name;
        $username = $name ?: $user->username;

        $avatar = load_img('users/avatar-1.jpg');

        $this->users = view('admin::index.user', ['name' => $username, 'avatar' => &$avatar])->render();

    }

    public function render()
    {
        $content = '';
        if ($this->content) {
            $content = $this->content->build();
        }

        if (! $this->users) {
            $this->setupUsers();
        }

        return view($this->view, [
                'maxTab' => $this->maxTab,
                'topbar' => &$this->topbar,
                'users' => &$this->users,
                'content' => &$content,
                'homeUrl' => &$this->homeUrl,
                'menuTitle' => &$this->menuTitle,
            ])
            ->render();
    }

}
