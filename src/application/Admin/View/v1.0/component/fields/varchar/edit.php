<div class="form-group clearfix">
    <label class="col-md-<?php echo empty($labelCol) ? 2 : $labelCol;?> control-label"><?php echo trans($label, 'fields'); ?></label>
    <div class="col-md-<?php echo empty($formCol) ? 8 : $formCol;?>">
        <input value="<?php echo isset($value) ? $value : '';?>" type="text" placeholder="<?php echo empty($placeholder) ? '' : trans($placeholder);?>" name="<?php echo $name;?>" class="form-control" >
        <span class="help-block" style="color:#737373"><small><?php echo isset($help) ? $help : '';?></small></span>
    </div>
</div>