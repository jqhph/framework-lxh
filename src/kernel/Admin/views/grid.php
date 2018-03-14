<?php
$orginUrl = $url->string();

if ($filter) {?><div style="height:9px"></div><div id="<?php echo $filterId?>"><?php echo $filter;?></div><div class="clearfix"></div><div style="margin:15px 0"></div><?php } ?>
<div id="<?php echo ($pjid = Lxh\Admin\Grid::getPjaxContainerId());?>"><?php
    echo view('admin::grid-content', [
        'content' => &$content,
        'pageString' => &$pageString,
        'pageOptions' => &$pageOptions,
        'perPageKey' => &$perPageKey,
        'useRWD' => &$useRWD,
        'pjax' => &$pjax,
        'perPage' => &$perPage,
        'url' => $url
    ])->render();
?></div>
<script>
(function (w) {
    var n = NProgress;
    w.change_pages = function(){};
    // 刷新网格列表函数
    w.reload_grid = function () {
        n.start();
        $.get('<?php echo $orginUrl?>',pjax_set);
    };
    
    <?php if ($pageString) {?>
    __then__(function () {
        w.change_pages = function () {
            <?php if ($pjax) { ?>
            n.start();
            $.get($(this).val(),pjax_set);
            <?php } else {
            echo 'w.location.href = $(this).val();';
        } ?>
        };

        $('.grid-per-pager').change(w.change_pages);
    });
    <?php }?>

    function pjax_set(d) {
        $('#pjax-container').html(d);
        $(document).trigger('pjax:complete',{});
        n.done()
    }
})(window);
</script>