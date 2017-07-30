<?php
if (! isset($value)) {
    $value = null;
}
?>
<div class="<?php echo isset($formGroup) ? $formGroup : 'form-group' ;?> clearfix">
    <?php if (! empty($label)) {?>
    <label class="col-md-<?php echo empty($labelCol) ? 2 : $labelCol;?> control-label"><?php echo trans($label, 'fields'); ?></label>
    <?php  }?>
    <div class="col-md-<?php echo empty($formCol) ? 8 : $formCol;?>">
        <select name="<?php echo $name;?>" class="form-control">
            <?php foreach ($list as & $r) { ?>
                <option <?php if ($value == $r['value']) echo 'selected';?> value="<?php echo $r['value'];?>"><?php echo trans_option($r['value'], $name); ?></option>
            <?php  }?>
        </select>
    </div>
</div>