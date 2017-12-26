<script>
    (function (w) {
        w.tab = function () {
            var $top = w.$top || w.top.$top
            return $top.tab
        }
        w.open_tab = function (id, url, label) {
            tab().switch(id, url, label)
        }
        w.close_tab = function (id) {
            tab().close(id)
        }
        w.back_tab = function (step) {
            tab().back(step)
        }
    })(window)
</script>
<?php
echo render_view('public.app-js');
