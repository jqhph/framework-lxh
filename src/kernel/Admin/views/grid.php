<div id="pjax-container"><?php
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
            var $loading = loading($('#pjax-container').parent());
            $.get($(this).val(), function (data) {
                $('#pjax-container').html(data);
                $(document).trigger('pjax:complete', {});
                $loading.close()
            });
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