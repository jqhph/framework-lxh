<?php

namespace Lxh\Install;

use Lxh\Admin\Admin;
use Lxh\Admin\Layout\Content;
use Lxh\Admin\Layout\Row;
use Lxh\Admin\Widgets\Card;
use Lxh\Admin\Widgets\Form;
use Lxh\ORM\Connect\PDO;

class Step2
{
    /**
     * @var Content
     */
    protected $content;

    public function __construct(Content $content)
    {
        $this->content = $content->page(true);

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
            $form->setSubmitBtnLabel('&nbsp;&nbsp;&nbsp;&nbsp;'.trans('Next').'&nbsp;&nbsp;&nbsp;&nbsp;');

            $form->text('host')->required()->default('localhost')->width(6);
            $form->text('port')->required()->default(3306)->width(6);
            $form->text('user')->required()->default('root')->width(6);
            $form->text('password')->width(6);
            $form->text('dbname')->required()->default('lxh')->width(6);
            $form->text('charset')->required()->default('utf8')->width(6);

            $card = new Card(
                view('install::step2.index', ['form' => $form])->render()
            );
            
            $row->column(12, view('install::content', ['card' => $card])->render());
        });


        return $this->content->render();
    }

    /**
     * 设置数据库配置文件并连接测试
     *
     * @return array
     */
    public function setupDatabaseConfig()
    {
        $config = [
            'usepool' => false,
            'type'    => 'mysql',
            'host'    => I('host'),
            'port'    => I('port', 3306),
            'user'    => I('user'),
            'pwd'     => (string)I('password'),
            'charset' => I('charset', 'utf8'),
            'name'    => I('dbname'),
        ];

        try {
            $pdo = new PDO($config);
        } catch (\PDOException $e) {
            switch ($e->getCode()) {
                case 1045:
                    // 登录信息错误
                    return $this->responseError(
                        trans('Error establishing a database connection'),
                        view('install::step2.1045', ['config' => &$config])->render()
                    );
                case 1049:
                    // 数据库错误
                    return $this->responseError(
                        trans('Can&#8217;t select database'),
                        view('install::step2.1049', ['config' => &$config])->render()
                    );
            }
        }

        $configPath = __CONFIG__.'dev/database.php';

        if (files()->putPhpContents($configPath, [
            'primary' => &$config
        ], true)) {
            // 成功
            $this->content->row(function (Row $row) {
                $tip = trans('All right, sparky! Lxh Framework can now communicate with your database.');
                $btn = trans('Let&#8217;s go!');
                $card = new Card(
                    "<p>$tip</p><br>
<a href='/install/3' class='btn btn-primary'>&nbsp;&nbsp;&nbsp;&nbsp;$btn&nbsp;&nbsp;&nbsp;&nbsp;</a>
"
                );

                $row->column(12, view('install::content', ['card' => $card])->render());
            });

            return $this->content->render();
        }

        return $this->responseError(
            sprintf(
                trans('Sorry, you can&#8217;t write the %s file.'),
                '<code>'.$configPath.'</code>'
            ),
            ''
        );
    }

    /**
     * @param $title
     * @param $content
     * @return string
     */
    protected function responseError($title, $content)
    {
        Admin::style('body{color:#444}');

        $this->content->row(function (Row $row) use ($title, $content) {
            $card = new Card($title, $content);

            $row->column(12, "<div style='margin:0 auto;padding:40px 29.5%'>{$card->render()}</div>");
        });


        return $this->content->render();
    }
}
