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
        <div class="checkbox checkbox-danger">
            <input value="1" name="<?php echo $name;?>" type="checkbox" <?php if ($value) echo 'checked'?>><label></label></div>
    </div>
</div>