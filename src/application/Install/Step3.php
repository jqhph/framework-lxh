<?php

namespace Lxh\Install;

use Lxh\Admin\Admin;
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
                ->minlen(5)
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
        $username = I('admin_username');
        $password = I('admin_password');

        
    }

    protected function success($username)
    {
        $this->content->row(function (Row $row) use ($username) {
            $tip    = trans('Lxh Framework has been installed. Thank you, and enjoy!');
            $btn    = trans('Sign in');
            $prefix = config('admin.route-prefix');

            $usernameLabel = trans('Username');
            $passwordLabel = trans('Password');
            $password      = trans('Your chosen password.');

            Admin::style('td,th{border:0!important;}');

            $card = new Card(
                trans('Success!'),
                "<p>$tip</p>
<table class=\"table install-success\">
	<tbody><tr>
		<th>{$usernameLabel}</th>
		<td>$username</td>
	</tr>
	<tr>
		<th>$passwordLabel</th>
		<td> <p><em>$password</em></p>
		</td>
	</tr>
</tbody></table>
                <br>
                <a href='/$prefix/login' class='btn btn-primary'>&nbsp;&nbsp;&nbsp;&nbsp;$btn&nbsp;&nbsp;&nbsp;&nbsp;</a>
"
            );

            $row->column(12, view('install::content', ['card' => $card])->render());
        });

        return $this->content->render();
    }

    protected function already()
    {
        $this->content->row(function (Row $row) {
            $tip    = trans('You appear to have already installed Lxh Framework. To reinstall please clear your old database tables first.');
            $btn    = trans('Sign in');
            $prefix = config('admin.route-prefix');

            $card = new Card(
                trans('Already Installed'),
                "<p>$tip</p>
                <br>
                <a href='/$prefix/login' class='btn btn-primary'>&nbsp;&nbsp;&nbsp;&nbsp;$btn&nbsp;&nbsp;&nbsp;&nbsp;</a>
"
            );

            $row->column(12, view('install::content', ['card' => $card])->render());
        });

        return $this->content->render();
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
