<div class="form-group line col-md-<?php echo $width['layout']?>">
    <div class="col-sm-<?php echo $width['field'] ?>">
        <div class="text"><?php echo $prepend ? $prepend . '&nbsp; ' : ''; ?><?php echo $label ?></div>
        <div class="input-group" <?php if (!$append) {echo 'style="width:100%"';}?>>
            <input <?php echo $attributes ?> />
            <?php if ($append) {?>
            <span class="input-group-addon clearfix"><?php echo $append ?></span>
            <?php } ?>
            <?php if ($options) {?>
                <ul class="dropdown-menu col-sm-12" style="max-height:305px;">
                    <?php foreach ((array)$options as &$v) {?>
                        <li><a><?php echo $v;?></a></li>
                    <?php }?>
                </ul>
            <?php }?>
        </div>
        <?php if ($help) {
            echo ' <div class="clearfix"></div>';
            echo view('admin::form.help-block', ['help' => &$help])->render();
        }?>
    </div>
</div>