<div class="form-group ">
    <label for="<?php echo $id;?>" class="col-sm-<?php echo $width['label']?> control-label"><?php echo $label?></label>
    <div class="col-sm-<?php echo $width['field']?>">
        <input type="file" class="<?php echo $class;?>" name="<?php echo $name?>" <?php echo $attributes?> />
        <?php if ($help) {
            echo view('admin::form.help-block', ['help' => &$help])->render();
        }?>
    </div>
</div>