<div class="form-group ">
    <label for="<?php echo $id ?>" class="col-sm-<?php echo $width['label'] ?> control-label"><?php echo $label ?></label>
    <div class="col-sm-<?php echo $width['field'] ?>">s
        <div class="input-group">
            <?php if ($prepend) {?>
            <span class="input-group-addon"><?php echo $prepend ?></span>
            <?php } ?>
            <input <?php echo $attributes ?> />
            <?php if ($append) {?>
            <span class="input-group-addon clearfix"><?php echo $append ?></span>
            <?php } ?>
        </div>
        <?php if ($help) {
            echo view('admin::form.help-block')->render();
        }?>
    </div>
</div>