<div class="form-group clearfix">
    <label for="<?php echo $id ?>" class="col-sm-<?php echo $width['label'] ?> control-label"><?php echo $label ?></label>

    <div class="col-sm-<?php echo $width['field'] ?>">
        <table class="table table-bordered m-0">
            <?php foreach ($options as &$r) {
                if (! empty($r['title'])) {
                    ?>
                    <tr><th colspan="<?php echo $columnsNum?>"><?php echo trans($r['title'], 'fields')?></th></tr>
                <?php }

                $rowsEnd = count($r['rows']) - 1;
                foreach ($r['rows'] as $k => &$items) { ?>
                    <tr>
                        <?php
                        // 计算colspan
                        $itemsNum = count($items);
                        $tdColumns = $columnsNum - $itemsNum + 1;
                        $len = $itemsNum - 1;

                        foreach ($items as $k => &$v) { ?>
                            <td <?php echo $k == $len ? "colspan='$tdColumns'" : '';?>><?php if ($v) {
                                    $checked = '';
                                    if (in_array($v['value'], (array)$value)) {
                                        $checked = 'checked';
                                    }
                                    ?><div class="checkbox pull-left"><?php echo trans($v['label'], 'fields')?></div>
                                    <div <?php echo $attributes;?>>
                                        <input <?php if ($checked) echo 'checked';?> name="<?php echo $name ?>[]" type="checkbox" value="<?php echo $v['value']?>" ><label></label></div> <?php } ?></td>
                        <?php } ?>
                    </tr>
                <?php }  ?>
<!--                <tr><td colspan="--><?php //echo $columnsNum?><!--" style="border-left: 1px solid #fff;border-right: 1px solid #fff;"></td></tr>-->
            <?php }  ?>
        </table>
        <?php if ($help) {
            echo view('admin::form.help-block', ['help' => &$help])->render();
        }?>
    </div>
</div>