<?php
use Lxh\Admin\Kernel\Url;
?>
<div class="table-rep-plugin">
    <div class="table-responsive" data-pattern="priority-columns"><?php echo $table?></div>
</div>
<script>
    <?php if ($useRWD) {?>
    add_css('lib/plugins/RWD-Table-Patterns/dist/css/rwd-table.min.css');
    add_js('lib/plugins/RWD-Table-Patterns/dist/js/rwd-table');
    <?php }?>
    <?php if ($usePublicJs) {?>
    add_js('view/public-index');
    <?php }?>
</script>