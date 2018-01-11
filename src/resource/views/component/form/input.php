<div class="form-group ">
    <label for="<?php echo $id ?>" class="col-sm-<?php echo $width['label'] ?> control-label"><?php echo $label ?></label>
    <div class="col-sm-<?php echo $width['field'] ?>">
        <div class="input-group">
            <?php if ($prepend) {?>
            <span class="input-group-addon"><?php echo $prepend ?></span>
            <?php } ?>
            <input <?php echo $attributes ?> />
            <?php if ($append) {?>
            <span class="input-group-addon clearfix"><?php echo $append ?></span>
            <?php } ?>
            <?php if ($options) {?>
                <ul class="dropdown-menu col-sm-12">
                    <?php foreach ((array)$options as &$v) {?>
                        <li><a><?php echo $v;?></a></li>
                    <?php }?>
                </ul>
            <?php }?>
        </div>
        <?php if ($help) {
            echo view('admin::form.help-block', ['help' => &$help])->render();
        }?>
    </div>
</div>