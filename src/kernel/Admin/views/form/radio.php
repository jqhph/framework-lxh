<div class="form-group line">
    <div class="col-sm-<?php echo $width['field'] ?>">
        <div class="text"><?php echo $prepend ? $prepend . '&nbsp; ' : ''; ?><?php echo $label ?></div>
        <div class="input-group" <?php echo 'style="width:100%"';?>>
            <?php foreach ($options as &$opt) {
                $id = 'r'.Lxh\Helper\Util::randomString(6);
                ?>
            <div class="<?php echo $type?> <?php echo $type?>-<?php echo $color;?> <?php echo $inline?>">
                <input id="<?php echo $id?>" <?php echo $attributes;?> <?php if ($value == $opt['value']) echo 'checked'; ?> name="<?php echo $name;if ($type == 'checkbox') echo '[]';?>" value="<?php echo $opt['value']?>">
                <label for="<?php echo $id?>"><?php echo $opt['label']?></label>
            </div>
            <?php } ?>
        </div>
        <?php if ($help) {
            echo ' <div class="clearfix"></div>';
            echo view('admin::form.help-block', ['help' => &$help])->render();
        }?>
    </div>
</div>
