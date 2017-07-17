<div class="form-group">
    <label class="col-md-2 control-label"><?php echo trans($label, 'fields'); ?></label>
    <div class="col-md-8"><div class=" input-group">
        <input value="<?php echo empty($value) ? '' : $value;?>" type="text" placeholder="<?php echo empty($placeholder) ? '' : $placeholder;?>" name="<?php echo $name;?>" class="form-control" >
        <span class="input-group-btn">
        <button data-val="<?php echo empty($value) ? '' : $value;?>" data-action="enum-select" data-id="<?php echo $id;?>" type="button" class="btn waves-effect waves-light btn-primary"><i class="glyphicon glyphicon-arrow-up"></i></button>
    </span>
    </div></div>
</div>