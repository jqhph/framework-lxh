<div id="<?php echo ($pjid = Lxh\Admin\Grid::getPjaxContainerId());?>"><?php
    echo view('admin::grid-content', [
        'table' => &$table,
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
    var PJAXID = '<?php echo $pjid?>';
    <?php if ($useRWD) {?>
    require_css('@lxh/plugins/RWD-Table-Patterns/dist/css/rwd-table.min');
    require_js('@lxh/plugins/RWD-Table-Patterns/dist/js/rwd-table.min');
    <?php }?>
    <?php if ($indexScript) {?>
    require_js('<?php echo $indexScript;?>');
    <?php }?>
    <?php if ($pageOptions) {?>
    __then__(function () {
        var _p = $('.grid-per-pager');
        _p.off('change');
        _p.change(change);
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
    __then__(function () {
        var $d = $(document), cid = '#<?php echo $pjid?>';
        $.pjax.defaults.timeout = 10000;
        $.pjax.defaults.maxCacheLength = 0;
        $d.pjax(cid + ' a:not(a[target="_blank"])', {container: cid});
        $d.on('submit', 'form[pjax-container]', function(e) {$.pjax.submit(e, cid)});
        $d.on("pjax:popstate", function() {
            $d.one("pjax:end", function(e) {
                $(e.target).find("script[data-exec-on-popstate]").each(function() {
                    $.globalEval(this.text || this.textContent || this.innerHTML || '');
                });
            });
        });
        var $loading, $current = TAB.currentEl();
        $d.on('pjax:send', function(xhr) {
            NProgress.start();
            $current = TAB.currentEl();
            if(xhr.relatedTarget && xhr.relatedTarget.tagName && xhr.relatedTarget.tagName.toLowerCase() === 'form') {
                var $submit_btn = $('form[pjax-container] :submit');
                if($submit_btn) $submit_btn.button('loading');
            }
            $loading = loading($('#pjax-container').parent());
        });
        $d.on('pjax:complete', function(xhr) {
            NProgress.done();
            if(xhr.relatedTarget && xhr.relatedTarget.tagName && xhr.relatedTarget.tagName.toLowerCase() === 'form') {
                var $submit_btn = $('form[pjax-container] :submit');
                if($submit_btn) $submit_btn.button('reset');
            }
            $loading && $loading.close();
            // 重新绑定点击事件
            var _p = $('.grid-per-pager');
            _p.off('change');
            _p.change(change_pages);
            // 重新计算iframe高度
            IFRAME.height($current.iframe.find('iframe'));
        })
    });

    <?php } ?>
</script>