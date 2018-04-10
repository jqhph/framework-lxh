<div class="form-group clearfix line col-md-<?php echo $width['layout']?>">
    <div class="col-sm-<?php echo $width['field'] ?>">
        <div class="text"><?php echo $prepend ? $prepend . '&nbsp; ' : ''; ?><?php echo $label ?></div>
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
                                    $id = 't'.Lxh\Helper\Util::randomString(6);
                                    ?>
                                    <div <?php echo $attributes;?>>
                                        <input id="<?php echo $id?>" <?php if ($checked) echo 'checked';?> name="<?php echo $name ?>[]" type="checkbox" value="<?php echo $v['value']?>" >
                                        <label for="<?php echo $id?>"><?php echo trans($v['label'], 'fields')?></label></div> <?php } ?></td>
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