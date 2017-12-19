<?php

namespace Lxh\Admin\Chat;

/**
 * 聊天窗
 */
class Room
{
    /**
     * @var string
     */
    protected $view = 'admin::chat.room';

    /**
     * @var array
     */
    protected $data = [];

    public function __construct(array $options = [])
    {
        $this->data = [
            'username' => admin()->username,
            'status' => 'online',
            'statusName' => '在线',
            'heads' => load_img('users/avatar-1.jpg'),
        ];

        $this->data = array_merge($this->data, $options);
    }

    public function render()
    {
        $this->data['jsHtml'] = $this->bladeJsHtmlView();

        return view($this->view, $this->data)->render();
    }

    /**
     * Js模板
     *
     * @return string
     */
    protected function bladeJsHtmlView()
    {
        return view('admin::chat.js-html')->render();
    }
}
