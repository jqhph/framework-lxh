<div class="form-group clearfix">
    <label class="col-md-2 control-label"><?php echo trans($label, 'fields'); ?></label>
    <div class="col-md-8">
        <?php foreach ($rows as & $r) { ?>
            <div class="checkbox checkbox-danger" style="display: inline;">
                <input value="<?php echo $r['value'];?>" name="<?php echo $name;?>[]" type="checkbox" <?php if (! empty($r['checked'])) echo 'checked'?>><label>
                    <?php echo trans($r['label']); ?></label></div>
            &nbsp;
        <?php } ?>
    </div>
</div>