<?php

namespace Lxh\Admin\Grid;

use Lxh\Admin\Admin;
use Lxh\Admin\Fields\Button;
use Lxh\Admin\Grid;

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
var \$gswc = $('#{$spaid}').find('.grid-switcher');
\$gswc.click(function () {
    \$gswc.removeClass('active');
    var t = $(this); t.addClass('active');
    pjax_reloads['$pjaxid'](null,t.data('url'));
});
EOF
        );
    }

    /**
     * @return string
     */
    public function render()
    {
        $tableView = $this->url->query('view', 'table')->string();
        $cardView = $this->url->query('view', 'card')->string();

        $tableActive = $cardActive = '';
        if ($this->grid->getLayout() == Grid::LAYOUT_CARD) {
            $cardActive = 'active';
        } else {
            $tableActive = 'active';
        }

        return "<button data-url=\"$tableView\" class=\"grid-switcher waves-effect btn btn-default $tableActive\"><i class=\"fa fa-list\"></i></button><button data-url=\"$cardView\" class=\"grid-switcher waves-effect btn btn-default $cardActive\"><i class=\"fa fa-th\"></i></button>";
    }
}
