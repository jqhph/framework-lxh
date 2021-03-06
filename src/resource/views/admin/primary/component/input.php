<?php
$value    = isset($value) ? $value : null;
$hideLabe = isset($hideLabe) ? $hideLabe : false;
$labelCol = empty($labelCol) ? 2 : $labelCol;
$formCol  = empty($formCol) ? 8 : $formCol;
$disabled = isset($disabled) ? $disabled : false;
?>
<div class="form-group clearfix">
    <?php if (! $hideLabe) {?>
        <label class="col-md-<?php echo $labelCol;?> control-label"><?php echo trans($name, 'fields'); ?></label>
    <?php }?>
    <div class="col-md-<?php echo $formCol;?>">
        <input <?php if ($disabled) echo 'disabled';?>  value="<?php echo $value;?>" type="text" placeholder="<?php echo empty($placeholder) ? '' : trans($placeholder);?>" name="<?php echo $name;?>" class="form-control" >
        <span class="help-block" style="color:#737373"><small><?php echo isset($help) ? $help : '';?></small></span>
    </div>
</div>