<div class="form-group clearfix">
    <label class="col-md-2 control-label"><?php echo trans($label, 'fields'); ?></label>
    <div class="col-md-8">
        <select name="<?php echo $name;?>" class="form-control">
            <?php foreach ($options as & $r) { ?>
                <option value="<?php echo $r['value'];?>"><?php echo trans_option($r['value'], $name); ?></option>
            <?php  }?>
        </select>
    </div>
</div>