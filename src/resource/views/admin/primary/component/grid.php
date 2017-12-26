<?php
use Lxh\Admin\Kernel\Url;
?>
<div class="card-box">
    <div class="table-rep-plugin">
        <div class="btn-toolbar"><div class="btn-group dropdown-btn-group pull-right"><?php echo $createBtn; ?></div></div>
        <div class="table-responsive" data-pattern="priority-columns"><?php echo $table?></div>
    </div>
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