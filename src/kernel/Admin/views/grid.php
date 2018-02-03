<div id="<?php echo ($pjid = Lxh\Admin\Grid::getPjaxContainerId());?>"><?php
    echo view('admin::grid-content', [
        'table' => &$table,
        'page' => &$page,
        'pages' => &$pages,
        'perPageKey' => &$perPageKey,
        'useRWD' => &$useRWD,
        'pjax' => &$pjax,
        'perPage' => &$perPage,
        'url' => $url
    ])->render();
?></div>
<script>
    var PJAXID = '<?php echo $pjid?>';
    <?php if ($useRWD) {?>
    require_css('@lxh/plugins/RWD-Table-Patterns/dist/css/rwd-table.min');
    require_js('@lxh/plugins/RWD-Table-Patterns/dist/js/rwd-table.min');
    <?php }?>
    <?php if ($indexScript) {?>
    require_js('<?php echo $indexScript;?>');
    <?php }?>
    <?php if ($pages) {?>
    __then__(function () {
        $('.grid-per-pager').change(change);
        function change() {
            <?php if ($pjax) { ?>
            NProgress.start();
            $.get($(this).val(),function(d){$('#<?php echo $pjid;?>').html(d);$(document).trigger('pjax:complete',{});NProgress.done()});
            <?php } else {
            echo 'window.location.href = $(this).val();';
        } ?>
        }
        window.change_pages = change
    });
    <?php }?>
    <?php if ($pjax) {
    // jquery.pjax.min含自定义js
    ?>
    require_js('@lxh/js/jquery.pjax.min');
    <?php } ?>
</script>