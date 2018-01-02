<div id="pjax-container"><?php echo view('admin::grid-content')->render();?></div>
<script>
    <?php if ($useRWD) {?>
    add_css('lib/plugins/RWD-Table-Patterns/dist/css/rwd-table.min.css');
    add_js('lib/plugins/RWD-Table-Patterns/dist/js/rwd-table.min');
    <?php }?>
    <?php if ($usePublicJs) {?>
    add_js('view/public-index');
    <?php }?>
    <?php if ($pages) {?>
    add_action(function () {
        $('.grid-per-pager').change(function () {
            window.location.href = $(this).val()
            $.getJSON($(this).val(), )
        })
    })
    <?php }?>
</script>
<?php if ($pjax) {?>
<script>
    add_js('jquery.pjax.min');
    add_action(function () {
        $.pjax.defaults.timeout = 5000;
        $.pjax.defaults.maxCacheLength = 0;
        $(document).pjax('a:not(a[target="_blank"])', {
            container: '#pjax-container'
        });
        $(document).on('submit', 'form[pjax-container]', function(event) {
            $.pjax.submit(event, '#pjax-container')
        });
        $(document).on("pjax:popstate", function() {
            $(document).one("pjax:end", function(event) {
                $(event.target).find("script[data-exec-on-popstate]").each(function() {
                    $.globalEval(this.text || this.textContent || this.innerHTML || '');
                });
            });
        });
        var $loading;
        $(document).on('pjax:send', function(xhr) {
            if(xhr.relatedTarget && xhr.relatedTarget.tagName && xhr.relatedTarget.tagName.toLowerCase() === 'form') {
                var $submit_btn = $('form[pjax-container] :submit');
                if($submit_btn) $submit_btn.button('loading');
            }
            $loading = loading('#lxh-app');
        })
        $(document).on('pjax:complete', function(xhr) {
            if(xhr.relatedTarget && xhr.relatedTarget.tagName && xhr.relatedTarget.tagName.toLowerCase() === 'form') {
                var $submit_btn = $('form[pjax-container] :submit');
                if($submit_btn) $submit_btn.button('reset');
            }
            $loading.close()
        })
    })
</script>
<?php } ?>