<?php

namespace Lxh\Install;

use Lxh\Admin\Layout\Content;
use Lxh\Admin\Layout\Row;
use Lxh\Admin\Widgets\Alert;
use Lxh\Admin\Widgets\Card;

class Step1
{
    /**
     * @var Content
     */
    protected $content;

    /**
     * @var Validator
     */
    protected $validator;

    /**
     * @var string
     */
    protected $helperUrl = 'https://github.com/jqhph/framework-lxh';

    public function __construct(Content $content)
    {
        $this->content   = $content->page(true);
        $this->validator = new Validator($this->helperUrl);

        add_view_namespace('install', __DIR__.'/resource/views');
    }

    /**
     * @return string
     */
    public function build()
    {
        if (!$this->validator->validate()) {
            return $this->responseError(
                trans('Insufficient Requirements'),
                $this->validator->getError()
            );
        }

        $this->content->row(function (Row $row) {
            $card = new Card(
                view('install::step1.index', ['helper' => $this->helperUrl])->render()
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
