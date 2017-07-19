<div class="form-group clearfix">
    <label class="col-md-2 control-label"><?php echo trans($label, 'fields'); ?></label>
    <div class="col-md-8">
        <input value="<?php echo isset($value) ? $value : '';?>" type="text" placeholder="<?php echo empty($placeholder) ? '' : $placeholder;?>" name="<?php echo $name;?>" class="form-control" >
    </div>
</div>