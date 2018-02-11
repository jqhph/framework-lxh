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
    <?php if ($indexScript) {?>
    require_js('<?php echo $indexScript;?>');
    <?php }?>
    <?php if ($pageString) {?>
    __then__(function () {
        $('.grid-per-pager').change(change);
        function change() {
            <?php if ($pjax) { ?>
            NProgress.start();
            $.get($(this).val(),function(d){$('#pjax-container').html(d);$(document).trigger('pjax:complete',{});NProgress.done()});
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