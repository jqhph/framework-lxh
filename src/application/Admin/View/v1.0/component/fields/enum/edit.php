<?php
$value    = isset($value) ? $value : null;
$hideLabe = isset($hideLabe) ? $hideLabe : false;
$labelCol = empty($labelCol) ? 2 : $labelCol;
$formCol  = empty($formCol) ? 8 : $formCol;

?>
<div class="<?php echo isset($formGroup) ? $formGroup : 'form-group' ;?> clearfix">
    <?php if (! $hideLabe) {?>
    <label class="col-md-<?php echo $labelCol;?> control-label"><?php echo trans($name, 'fields'); ?></label>
    <?php }?>
    <div class="col-md-<?php echo $formCol;?>">
        <select name="<?php echo $name;?>" class="form-control">
            <?php foreach ($opts as & $r) { ?>
                <option <?php if ($value == $r) echo 'selected';?> value="<?php echo $r;?>"><?php echo trans_option($r, $name); ?></option>
            <?php  }?>
        </select>
    </div>
</div>