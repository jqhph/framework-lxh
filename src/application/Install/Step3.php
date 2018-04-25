<?php

namespace Lxh\Install;

use Lxh\Admin\Layout\Content;
use Lxh\Admin\Layout\Row;
use Lxh\Admin\Widgets\Alert;
use Lxh\Admin\Widgets\Card;
use Lxh\Admin\Widgets\Form;

class Step3
{
    /**
     * @var Content
     */
    protected $content;

    public function __construct(Content $content)
    {
        $this->content   = $content->page(true);

        add_view_namespace('install', __DIR__.'/resource/views');
    }

    /**
     * @return string
     */
    public function build()
    {
        $this->content->row(function (Row $row) {
            $form = new Form();

            $form->disableEditScript();
            $form->disableReset();
            $form->setSubmitBtnLabel('&nbsp;&nbsp;&nbsp;&nbsp;'.trans('Install').'&nbsp;&nbsp;&nbsp;&nbsp;');

            $form->text('admin_username')
                ->required()
                ->default('admin')
                ->help(trans('Usernames can have only alphanumeric characters, spaces, underscores, hyphens, periods, and the @ symbol.'));
            $form->text('admin_password')
                ->required()
                ->help(trans('You will need this password to log&nbsp;in. Please store it in a secure location.'));

            $card = new Card(
                trans('Welcome'), view('install::step3.index', ['form' => $form])->render()
            );

            $row->column(12, view('install::content', ['card' => $card])->render());
        });

        return $this->content->render();
    }

    /**
     * 开始安装
     */
    public function install()
    {

    }

    /**
     * @param $title
     * @param $content
     * @return string
     */
    protected function responseError($title, $content)
    {
        $this->content->row(function (Row $row) use ($title, $content) {
            $alert = new Alert($content, $title, 'danger');

            $row->column(12, "<div style='margin:0 auto;padding:30px 25%'>{$alert->render()}</div>");
        });

        return $this->content->render();
    }
}
