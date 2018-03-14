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
        $this->url = clone $grid->getUrl();

        $filterId = '';
        if ($filter = $this->grid->filter()) {
            $filterId = $filter->getContainerId();
        }
        Admin::script(<<<EOF
(function () {
var st = LXHSTORE, \$g = $('.grid-switcher'), crt = st.IFRAME.current(), 
form = $('#{$filterId} form'), 
formUrl = form.attr('action').replace(/[&]*view=[-\w\d]*/i, '');
\$g.click(function () {
    var t = $(this), v = t.data('view');
    // 缓存
    st.cache.set(t.data('path'), v);
    form.attr('action', formUrl + '&view=' + v);
    st.TAB.reload(crt, t.data('url').replace(/[&]*_pjax=[#-\w\d]*/i, ''));
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

        $this->url->unsetQuery($this->grid->pjax);

        $tableActive = $cardActive = 'btn-default';
        if ($this->grid->getLayout() == Grid::LAYOUT_CARD) {
            $url = $this->url->query('view', 'table')->string();
            $icon = 'fa fa-list';
            $view = Grid::LAYOUT_TABLE;
        } else {
            $url = $this->url->query('view', 'card')->string();
            $icon = 'fa fa-th';
            $view = Grid::LAYOUT_CARD;

        }

        return "<div class=\"btn-group\"><button data-path='$origin' data-view='$view' data-url=\"$url\" class=\"grid-switcher waves-effect btn $tableActive\"><i class=\"$icon\"></i></button></div>";

    }
}
