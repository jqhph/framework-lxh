<?php

namespace Lxh\Admin\Grid;

use Lxh\Admin\Admin;
use Lxh\Admin\Fields\Button;
use Lxh\Admin\Grid;
use Lxh\Http\Url;

class LayoutSwitcher
{
    /**
     * @var Grid
     */
    protected $grid;

    /**
     * @var \Lxh\Http\Url
     */
    protected $url;

    public function __construct(Grid $grid)
    {
        $this->grid = $grid;
        $this->url = $grid->getUrl();

        $spaid = Admin::SPAID();
        $pjaxid = Grid::getPjaxContainerId();

        $filterId = '';
        if ($filter = $this->grid->filter()) {
            $filterId = $filter->getContainerId();
        }
        Admin::script(<<<EOF
(function () {
var \$g = $('#{$spaid}').find('.grid-switcher'), crt = LXHSTORE.IFRAME.current(), 
form = $('#{$filterId} form'), 
formUrl = form.attr('action').replace(/[&]*view=.*/i, '');
\$g.click(function () {
    \$g.removeClass('btn-custom');
    \$g.addClass('btn-default');
    var t = $(this), v = t.data('view'); t.addClass('btn-custom');t.removeClass('btn-default');
    // 缓存
    \$lxh.cache().set(t.data('path'), v);
    form.attr('action', formUrl + '&view=' + v);
    LXHSTORE.TAB.reload(crt, t.data('url').replace(/[&]*_pjax=.*/i, ''));
});
})();
EOF
        );
    }

    /**
     * @return string
     */
    public function render()
    {
        $origin = $this->url->uri()->getPath();

        $tableView = $this->url->query('view', 'table')->string();
        $cardView = $this->url->query('view', 'card')->string();

        $tableActive = $cardActive = 'btn-default';
        if ($this->grid->getLayout() == Grid::LAYOUT_CARD) {
            $cardActive = 'btn-custom';
        } else {
            $tableActive = 'btn-custom';
        }

        $tb = Grid::LAYOUT_TABLE;
        $card = Grid::LAYOUT_CARD;
        return "<button data-path='$origin' data-view='$tb' data-url=\"$tableView\" class=\"grid-switcher waves-effect btn $tableActive\"><i class=\"fa fa-list\"></i></button><button data-view='$card' data-path='$origin' data-url=\"$cardView\" class=\"grid-switcher waves-effect btn $cardActive\"><i class=\"fa fa-th\"></i></button>";
    }
}
