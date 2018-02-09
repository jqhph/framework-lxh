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
    if (!LXHSTORE.pjax_reloads) {
        LXHSTORE.pjax_reloads = {};
    }
    var PJAXID = '<?php echo $pjid?>';
    <?php if ($indexScript) {?>
    require_js('<?php echo $indexScript;?>');
    <?php }?>
    <?php if ($pageOptions) {?>
    __then__(function () {
        var _p = $('.grid-per-pager'), $d = $(document);
        _p.off('change');
        _p.change(pjax_reload);
        function pjax_reload(e, url) {
            <?php if ($pjax) { ?>
            NProgress.start();
            $.get(url||$(this).val(),function(d){$('#<?php echo $pjid;?>').html(d);$('#<?php echo $pjid?>').trigger('pjax:complete',{});NProgress.done();});
            <?php } else {
            echo 'window.location.href = $(this).val();';
        } ?>
        }
        LXHSTORE.pjax_reloads['<?php echo $pjid?>'] = pjax_reload
    });
    <?php }?>
    <?php if ($pjax) {
    // jquery.pjax.min含自定义js
    ?>
    require_js('@lxh/js/jquery.pjax.min');
    __then__(function () {
        var $d = $(document), cid = '#<?php echo $pjid?>', $c = $(cid);
        $.pjax.defaults.timeout = 10000;
        $.pjax.defaults.maxCacheLength = 0;
        $d.pjax(cid + ' a:not(a[target="_blank"])', {container: cid});
        <?php if ($filterId) {?>
        $d.on('submit', '#<?php echo $filterId;?> form[pjax-container]', function(e) {$.pjax.submit(e, cid);});
        <?php } ?>
        $c.on("pjax:popstate", function() {
            $d.one("pjax:end", function(e) {
                $(e.target).find("script[data-exec-on-popstate]").each(function() {
                    $.globalEval(this.text || this.textContent || this.innerHTML || '');
                });
            });
        });
        $c.on('pjax:send', function(xhr) {
            NProgress.start();
            if(xhr.relatedTarget && xhr.relatedTarget.tagName && xhr.relatedTarget.tagName.toLowerCase() === 'form') {
                var $submit_btn = $('form[pjax-container] :submit');
                if($submit_btn) $submit_btn.button('loading');
            }
        });
        $c.on('pjax:complete', function(xhr) {
            NProgress.done();
            if(xhr.relatedTarget && xhr.relatedTarget.tagName && xhr.relatedTarget.tagName.toLowerCase() === 'form') {
                var $submit_btn = $('form[pjax-container] :submit');
                if($submit_btn) $submit_btn.button('reset');
            }
            // 重新绑定点击事件
            var _p = $('.grid-per-pager');
            _p.off('change');
            _p.change(LXHSTORE.pjax_reloads['<?php echo $pjid?>']);
            $d.trigger('app.created');
        })
    });

    <?php } ?>
</script>