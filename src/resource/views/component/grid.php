<div class="table-rep-plugin"><div class="table-responsive" data-pattern="priority-columns"><?php echo $table?></div></div>
<?php if ($page) { ?>
    <div class="box-footer">
        <div class="dataTables_paginate paging_simple_numbers pull-center" style="float:right">
            <ul class="pagination" style="float:right"><?php echo $page;?></ul>
            <?php if ($pages) {?>
                <select class="input-sm grid-per-pager" name="per-page"  style="float:right;margin-top:10px;margin-right:10px;">
                    <?php foreach ($pages as &$row) {
                        $url = url()->query($perPageKey, $row)->string();
                        ?>
                        <option <?php if ($perPage == $row) echo 'selected';?> value="<?php echo $url?>"><?php echo $row;?></option>
                    <?php } ?>
                </select>
            <?php }?>
        </div>
        <div style="clear:both"></div>
    </div>
<?php }?>
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
        })
    })
    <?php }?>
</script>