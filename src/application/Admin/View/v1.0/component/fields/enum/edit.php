<div class="<?php echo isset($formGroup) ? $formGroup : 'form-group' ;?> clearfix">
    <?php if (! empty($label)) {?>
    <label class="col-md-2 control-label"><?php echo trans($label, 'fields'); ?></label>
    <?php  }?>
    <div class="<?php echo isset($formWidth) ? $formWidth : 'col-md-8';?>">
        <select name="<?php echo $name;?>" class="form-control">
            <?php foreach ($list as & $r) { ?>
                <option value="<?php echo $r['value'];?>"><?php echo trans_option($r['value'], $name); ?></option>
            <?php  }?>
        </select>
    </div>
</div>