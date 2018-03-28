<?php echo $content?>
<?php if ($pageString) { ?>
    <div class="box-footer">
        <div class="dataTables_paginate paging_simple_numbers pull-left">
            <ul class="pagination" style="float:right"><?php echo $pageString;?></ul>
            <?php if ($pageOptions) {?>
                <select class="input-sm grid-per-pager" name="per-page"  style="float:right;margin-top:10px;margin-right:10px;background:#fff;"><?php
                    foreach ($pageOptions as &$row) :
                        $string = $url->query($perPageKey, $row)->string();
                        ?><option <?php if ($perPage == $row) echo 'selected';?> value="<?php echo $string?>"><?php echo $row;?></option><?php endforeach;?></select>
            <?php }?>
        </div>
        <div style="clear:both"></div>
    </div>
<?php }?>
<?php if ($useRWD && $pjax && \Lxh\Admin\Grid::isPjaxRequest()) { ?>
<script>setTimeout("$('.table-responsive').responsiveTable({adddisplayallbtn: true});",200)</script>
<?php } ?>