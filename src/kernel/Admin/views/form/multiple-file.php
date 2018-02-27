<div class="form-group line">
    <div class="col-sm-<?php echo $width['field']?>">
        <div class="text"><?php echo $prepend ? $prepend . '&nbsp; ' : ''; ?><?php echo $label ?></div>
        <input type="file" class="<?php echo $class?>" name="<?php echo $name?>[]" <?php echo $attributes?> />
        <?php if ($help) {
            echo view('admin::form.help-block', ['help' => &$help])->render();
        }?>
    </div>
</div>
