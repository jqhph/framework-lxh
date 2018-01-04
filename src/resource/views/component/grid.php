<div id="pjax-container"><?php echo view('admin::grid-content')->render();?></div>
<script>
    <?php if ($useRWD) {?>
    require_css('lib/plugins/RWD-Table-Patterns/dist/css/rwd-table.min.css');
    require_js('lib/plugins/RWD-Table-Patterns/dist/js/rwd-table.min');
    <?php }?>
    <?php if ($indexScript) {?>
    require_js('<?php echo $indexScript;?>');
    <?php }?>
    <?php if ($pages) {?>
    __then__(function () {
        $('.grid-per-pager').change(change)
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
</script>
<?php if ($pjax) {?>
<script>
    require_js('jquery.pjax.min');
    __then__(function () {
        $.pjax.defaults.timeout = 5000;
        $.pjax.defaults.maxCacheLength = 0;
        $(document).pjax('#pjax-container a:not(a[target="_blank"])', {container: '#pjax-container'});
        $(document).on('submit', 'form[pjax-container]', function(event) {$.pjax.submit(event, '#pjax-container')});
        $(document).on("pjax:popstate", function() {
            $(document).one("pjax:end", function(event) {
                $(event.target).find("script[data-exec-on-popstate]").each(function() {
                    $.globalEval(this.text || this.textContent || this.innerHTML || '');
                });
            });
        });
        var $loading, $current = TAB.currentEl();
        $(document).on('pjax:send', function(xhr) {
            $current = TAB.currentEl();
            if(xhr.relatedTarget && xhr.relatedTarget.tagName && xhr.relatedTarget.tagName.toLowerCase() === 'form') {
                var $submit_btn = $('form[pjax-container] :submit');
                if($submit_btn) $submit_btn.button('loading');
            }
            $loading = loading($('#pjax-container').parent());
        })
        $(document).on('pjax:complete', function(xhr) {
            if(xhr.relatedTarget && xhr.relatedTarget.tagName && xhr.relatedTarget.tagName.toLowerCase() === 'form') {
                var $submit_btn = $('form[pjax-container] :submit');
                if($submit_btn) $submit_btn.button('reset');
            }
            $loading && $loading.close();
            // 重新绑定点击事件
            $('.grid-per-pager').change(change_pages);
            // 重新计算iframe高度
            IFRAME.height($current.iframe.find('iframe'));
        })
    })
</script>
<?php } ?>