<div class="form-group ">
    <div class="col-sm-<?php echo $width['field']?> line">
        <div class="text"><?php echo $prepend ? $prepend . '&nbsp; ' : ''; ?><?php echo $label ?></div>
        <input type="file" class="<?php echo $class;?>" data-value="<?php echo $value;?>" name="<?php echo $name?>" <?php echo $attributes?> />
        <input type="hidden" value="<?php echo $value;?>" name="<?php echo $name?>-origin"/>
        <?php if ($help) {
            echo view('admin::form.help-block', ['help' => &$help])->render();
        }?>
    </div>
</div>