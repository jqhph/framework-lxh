<?php
$value    = isset($value) ? $value : null;
$hideLabe = isset($hideLabe) ? $hideLabe : false;
$labelCol = empty($labelCol) ? 2 : $labelCol;
$formCol  = empty($formCol) ? 8 : $formCol;

?>
<div class="form-group clearfix">
    <?php if (! $hideLabe) {?>
    <label class="col-md-<?php echo $labelCol;?> control-label"><?php echo trans($name, 'fields'); ?></label>
    <?php }?>
    <div class="col-md-<?php echo $formCol;?>">
        <?php foreach ($opts as & $r) { ?>
            <div class="checkbox checkbox-danger" style="display: inline;">
                <input value="<?php echo $r['value'];?>" name="<?php echo $name;?>[]" type="checkbox" <?php if (! empty($r['checked'])) echo 'checked'?>><label>
                    <?php echo trans($r['value'], $name); ?></label></div> &nbsp;
        <?php } ?>
    </div>
</div>