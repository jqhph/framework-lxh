<?php

namespace Lxh\Admin\Controllers;

use Lxh\Admin\Cards\Cards;
use Lxh\Admin\Fields\Editable;
use Lxh\Admin\Fields\Expand;
use Lxh\Admin\Fields\Popover;
use Lxh\Admin\Fields\Image;
use Lxh\Admin\Http\Controllers\Controller;
use Lxh\Admin\Layout\Content;
use Lxh\Admin\Filter;
use Lxh\Admin\Grid;
use Lxh\Admin\Layout\Row;
use Lxh\Admin\Table\Column;
use Lxh\Admin\Table\Table;
use Lxh\Admin\Table\Td;
use Lxh\Admin\Table\Th;
use Lxh\Admin\Table\Tr;
use Lxh\Admin\Widgets\Box;
use Lxh\Admin\Widgets\Form;
use Lxh\Admin\Widgets\Tab;
use Lxh\Admin\Widgets\WaterFall;
use Lxh\Http\Request;
use Lxh\Http\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Post extends Controller
{
    /**
     * 使用过滤器
     *
     * @var string
     */
    protected $filter = 'modal';

    /**
     * @param Grid $grid
     * @param Content $content
     */
    protected function grid(Grid $grid, Content $content)
    {
        $grid->useCard();
        $grid->useLayoutSwitcher();
    }

    /**
     * 过滤器
     *
     * @param Filter $filter
     */
    protected function filter(Filter $filter)
    {
        $filter->text('id')->number();
        $filter->text('title')->minlen(3)->like();
    }

    /**
     * 表格布局
     *
     * @param Table $table
     */
    protected function table(Table $table)
    {
        $table->code('id');
        $table->text('title');
    }

    /**
     * 瀑布流卡片布局
     *
     * @param Cards $cards
     */
    public function card(Cards $cards)
    {
        // 获取当前卡片
        $each = $cards->each();

        // 设置瀑布流卡片过滤选项
        $cards->setFilterOptions(['Lxh', 'Jqh']);

        $each->image(
            $cards->image('img')
                ->width('auto')
                ->value("https://img3.doubanio.com/view/photo/s_ratio_poster/public/p2511434383.jpg")
        );

        $each->title(
            $cards->text('title')
        );

        $each->row(
            $cards->fieldLabel('id'), $cards->code('id')
        );
        $each->row(
            $cards->fieldLabel('content'), $cards->text('content')
        );

        // 设置当前卡片过滤属性
        $each->setFilters([$cards->item('author')]);
    }

    public function actionList111(array $params)
    {
        $content = $this->admin()->content();

        $content->header(trans(__CONTROLLER__));
        $content->description(trans(__CONTROLLER__ . ' list'));

        $content->row(function (Row $row) {
            $wf = new WaterFall();

            $wf->filters(['amsterdam', 'art', 'london', 'tokyo']);

            foreach (array_merge(range(1, 10), range(1, 10)) as $i) {
                $wf->card(function (WaterFall\Card $card) use ($i) {
                    $filter = [];
                    if ($i == 1 || $i == 3) {
                        $filter = ['art'];
                    }
                    if ($i == 2) {
                        $filter = ['tokyo', 'art'];
                    }
                    if ($i == 9 || $i == 3) {
                        $filter = ['amsterdam', 'art'];
                    }
                    if ($i == 2 || $i == 7) {
                        $filter = ['london', 'tokyo'];
                    }
                    $card->image("<img src='/test/image_{$i}.jpg'/>")
                        ->title('标题')
                        ->row('行内容')
                        ->row('左', '右')
                        ->row('左', '右')
                        ->row('行内容')
                        ->meta('LXH');

                    $card->setFilters($filter);

                });
            }

            $box = new Box();
            $box->content($wf->render())->style('inverse');

            $row->column(12, $box->render());
        });

        return $content->render();
    }
}
