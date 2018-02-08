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
        Admin::script(<<<EOF
(function () {
var \$g = $('#{$spaid}').find('.grid-switcher');
\$g.click(function () {
    \$g.removeClass('active');
    var t = $(this); t.addClass('active');
    pjax_reloads['$pjaxid'](null,t.data('url'));
    // 缓存
    \$lxh.cache().set(t.data('path'), t.data('view'));
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

        $tableActive = $cardActive = '';
        if ($this->grid->getLayout() == Grid::LAYOUT_CARD) {
            $cardActive = 'active';
        } else {
            $tableActive = 'active';
        }

        $tb = Grid::LAYOUT_TABLE;
        $card = Grid::LAYOUT_CARD;
        return "<button data-path='$origin' data-view='$tb' data-url=\"$tableView\" class=\"grid-switcher waves-effect btn btn-default $tableActive\"><i class=\"fa fa-list\"></i></button><button data-view='$card' data-path='$origin' data-url=\"$cardView\" class=\"grid-switcher waves-effect btn btn-default $cardActive\"><i class=\"fa fa-th\"></i></button>";
    }
}
