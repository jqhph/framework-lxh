<div class="form-group clearfix">
    <label class="col-md-<?php echo empty($labelCol) ? 2 : $labelCol;?> control-label"><?php echo trans($label, 'fields'); ?></label>
    <div class="col-md-<?php echo empty($formCol) ? 8 : $formCol;?>">
        <div class="checkbox checkbox-danger">
            <input value="1" name="<?php echo $name;?>" type="checkbox" <?php if ($value) echo 'checked'?>><label></label></div>
    </div>
</div>