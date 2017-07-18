<div class="form-group">
    <label class="col-md-2 control-label"><?php echo trans($label, 'fields'); ?></label>
    <div class="col-md-8">
        <div class="checkbox checkbox-danger">
            <input name="<?php echo $name;?>" type="checkbox" <?php if ($value) echo 'checked'?>><label></label></div>
    </div>
</div>